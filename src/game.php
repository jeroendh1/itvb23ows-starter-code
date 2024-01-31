<?php
include_once 'util.php';
class Game
{
    const OFFSET =
    [
        [0, 1],
        [0, -1],
        [1, 0],
        [-1, 0],
        [-1, 1],
        [1, -1]
    ];
    public $board;
    public $player;
    public $hand;
    private $dbHandler;
    private $hiveAI;

    public function __construct(DbHandler $dbHandler, HiveAI $hiveAI)
    {
        $this->dbHandler = $dbHandler;
        $this->hiveAI = $hiveAI;

        session_start();

        if (!isset($_SESSION['board'])) {
            $this->restart();
        }

        $this->board = $_SESSION['board'];
        $this->player = $_SESSION['player'];
        $this->hand = $_SESSION['hand'];

    }

    public function restart()
    {
        // Reset game state
        $this->board = [];
        $this->hand = [
            0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3],
            1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]
        ];
        $this->player = 0;

        // Save the new game state
        $_SESSION['board'] = $this->board;
        $_SESSION['hand'] = $this->hand;
        $_SESSION['player'] = $this->player;

        $_SESSION['game_id'] = $this->dbHandler->createNewGame();
    }

    public function play($piece, $to)
    {
        if(!$this->validatePlay($piece, $to)){
            return;
        }
        
        $this->board[$to] = [[$this->player, $piece]];
        $this->hand[$this->player][$piece]--;
        $this->player = 1 - $this->player;
        $this->updateGameState('play', $piece, $to);

    }

    public function move($from, $to)
    {
        $validMove = $this->validateMove($from, $to);
        if ($validMove) {
            $tile = array_pop($this->board[$from]);
            $this->board[$to] = [$tile];
            $this->player = 1 - $this->player;
            unset($this->board[$from]);
            
            $this->updateGameState('move', $from, $to);
        }
    }

    public function pass()
    {
        if ($this->validatePass()){
            $this->player = 1 - $this->player;
            $this->updateGameState('pass', null, null);
            return;
        }
        $this->setError("Not allowed to pass");
    }

    public function AI()
    {
        $numberOfMoves = $this->dbHandler->getGameMoves($_SESSION['game_id']);
        $aiMove = $this->hiveAI->suggestMove($numberOfMoves->num_rows, $this->hand, $this->board);
        
        if (!isset($aiMove)){
            return;
        }
        // Play, move or pass without validation
        if ($aiMove[0] == 'play'){
            $this->board[$aiMove[2]] = [[$this->player, $aiMove[1]]];
            $this->hand[$this->player][$aiMove[1]]--;
        }
        elseif ($aiMove[0] == 'move'){
            $tile = array_pop($this->board[$aiMove[1]]);
            $this->board[$aiMove[2]] = [$tile];
            unset($this->board[$aiMove[1]]);
        }
        $this->player = 1 - $this->player;
        $this->updateGameState(...$aiMove);
    }

    public function hasWinner()
    {
        $playerZeroLost = hasLostGame($this->board, 0);
        $playerOneLost = hasLostGame($this->board, 1);
        
        if ($playerOneLost && $playerZeroLost){
            return "Draw";
        }
        elseif($playerZeroLost){
            return "Black WINS!";
        }
        elseif($playerOneLost){
            return "White WINS!";
        }
    }

    public function getPositions(): array
    {
        $to = [];
        foreach (self::OFFSET as $pq) {
            foreach (array_keys($this->board) as $pos) {
                $pq2 = explode(',', $pos);
                $to[] = ($pq[0] + $pq2[0]).','.($pq[1] + $pq2[1]);
                 
            }
        }
        $to = array_unique($to);

        if (!count($to) && !count($this->board)) {
            $to[] = '0,0';
        }

        return $to;
    }

    public function getPossiblePositions(){
        $possible = [];
        foreach ($this->getPositions() as $to){
            if  ( $this->validatePosition($to)) {
                $possible[] = $to;
            }
        }
        return $possible;
    }

    public function getPlayerPositions(): array
    {
        $playerPositions = [];

        foreach ($this->board as $key => $value) {
            if (isset($value[0][0]) && $value[0][0] == $this->player) {
                $playerPositions[] = $key;
            }
        }
        
        return $playerPositions;
    }
    public function validatePosition($pos){
        if (isset($this->board[$pos])) {
            return false;
        }elseif(count($this->board) && !hasNeighBour($pos, $this->board)){
            return false;
        }
        elseif(array_sum($this->hand[$this->player]) < 11 && !neighboursAreSameColor( $this->player, $pos, $this->board) ) {
            return false;
        }
        return true;
    }

   
    public function validatePlay($piece, $to)
    {
        $valid = false;
        if (!$this->hand[$this->player][$piece]) {
            $this->setError("Player does not have tile");
        } elseif (isset($this->board[$to])) {
            $this->setError('Board position is not empty');
        } elseif (count($this->board) && !hasNeighBour($to, $this->board)) {
            $this->setError("board position has no neighbor");
        } elseif (array_sum($this->hand[$this->player]) < 11 && !neighboursAreSameColor( $this->player, $to, $this->board)) {
            $this->setError("Board position has opposing neighbor");
        } elseif ($piece != 'Q' && array_sum($this->hand[$this->player]) <= 8 && $this->hand[$this->player]['Q']) {
            $this->setError('Must play queen bee');
        } else { $valid = true;}

        return $valid;
    }

    public function validatePass()
    {  
        // Check if there are any possible positions to play a piece to
        if (count($this->getPossiblePositions()) > 0 && array_sum($this->hand[$this->player]) !== 0) {
            return false; // Player cannot pass yet
        }
       
        // Check if there are any valid moves the player can make with their current pieces on the board
        foreach ($this->board as $pos => $tiles) {
            if ($tiles[0][0] == $this->player) {
                // Check each possible position on the board
                foreach ($this->getPositions() as $to) {
                    if ($this->validateMove($pos, $to)) {
                        return false; // Player cannot pass yet
                    }
                }
            }
        }
        
        return true; // Player can pass
    }

    
    public function validateMove($from, $to)
    {
        if (!isset($this->board[$from])) {
            $this->setError('Board position is empty');
        } elseif ($from == $to) {
            $this->setError("Tile must move");
        } elseif (
            isset($this->board[$from][count($this->board[$from]) - 1]) &&
            $this->board[$from][count($this->board[$from]) - 1][0] != $this->player
        ) {
            $this->setError("Tile is not owned by player");
        } elseif ($this->hand[$this->player]['Q']) {
            $this->setError("Queen bee is not played");
        } else {
            $board = $this->board;
            $tile = array_pop($board[$from]);
            unset($board[$from]);

            if (!hasNeighbour($to, $board) ) {
                $this->setError("Move would split hive");
            } elseif (isset($board[$to]) && $tile[1] != "B") {
                $this->setError("Tile not empty");
            } elseif ( ($tile[1] == "Q" || $tile[1] == "B") && !slide($this->board,$from, $to))  {
                $this->setError("Tile must slide");
            } 
            elseif ( ($tile[1] == "G") && !GrasshopperMove($from, $to, $this->board))  {
                $this->setError("Tile must jump over at least one tile");
            }
            elseif ( ($tile[1] == "A") && !soldierAntMove($from, $to, $this->board))  {
                $this->setError("Tile must slide at least one tile");
            }
            elseif ( ($tile[1] == "S") && !spiderMove($from, $to, $this->board))  {
                $this->setError("Tile must move 3 times");
            } else {
                return true;
            }
        }
        return false;
    }

    public function undo()
    {
        $previousMoveId = $_SESSION['last_move'];
        $previousMove = $this->dbHandler->getMove($previousMoveId);

        if (!empty($previousMove)) {
            $result = $this->dbHandler->getMove($previousMove[5]);
            $this->dbHandler->deleteMove($previousMoveId);

            if (!$result) {
                $this->restart();
                return;
            }

            list($hand, $board, $player) = unserialize($result[6]);
            
            $this->board = $_SESSION['board'] = $board;
            $this->hand =  $_SESSION['hand'] = $hand ;
            $this->player = $_SESSION['player'] = $player ;
            $_SESSION['last_move'] =  $previousMove[5];
        }
    }

    private function setError($message)
    {
        $_SESSION['error'] = $message;
    }

    private function updateGameState($type, $from, $to)
    {
        $_SESSION['board'] = $this->board;
        $_SESSION['hand'] = $this->hand;
        $_SESSION['player'] = $this->player;
        $_SESSION['last_move'] = $this->dbHandler->saveMove($_SESSION['game_id'], $type, $from, $to, $_SESSION['last_move'],$this->getState());
    }

    private function getState()
    {
        return serialize([$this->hand, $this->board, $this->player]);
    }

  
    public function getCurrentPlayerColor()
    {
        return $this->player == 0 ? "White" : "Black";
    }

   
}
