<?php

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

    public function __construct(DbHandler $dbHandler)
    {
        $this->dbHandler = $dbHandler;
        session_start();
        include_once 'util.php';

        if (!isset($_SESSION['board'])) {
            header('Location: restart.php');
            exit(0);
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

        // $this->updateGameState();
    }

    public function move($from, $to)
    {
        $tile = $this->validateMove($from, $to);
        if ($tile) {    
            $this->board[$to] = [$tile];
            $this->player = 1 - $this->player;
            unset($this->board[$from]);
            
            $this->updateGameState('move', $from, $to);
        }
    }

    public function pass()
    {
        // $this->recordMove('pass', null, null);
        $this->player = 1 - $this->player;
        $this->updateGameState('pass', null, null);
    }
    public function getPossiblePositions(): array
    {
        $to = [];
        foreach (self::OFFSET as $pq) {
            foreach (array_keys($this->board) as $pos) {
                $pq2 = explode(',', $pos);
                $possible = ($pq[0] + $pq2[0]).','.($pq[1] + $pq2[1]);
                
                if  ( $this->validatePosition($possible)) {
                    $to[] =$possible;
                }
            }
        }
        $to = array_unique($to);

        if (!count($to) and !count($this->board)) {
            $to[] = '0,0';
        }

        return $to;
    }

    private function validatePosition($pos){
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
    private function validatePlay($piece, $to)
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
        } elseif (array_sum($this->hand[$this->player]) <= 8 && $this->hand[$this->player]['Q']) {
            $this->setError('Must play queen bee');
        } else { $valid = true;}

        return $valid;
    }

    private function validateMove($from, $to)
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
            $tile = array_pop($this->board[$from]);
            print_r($tile);
            if (!hasNeighbour($to, $this->board) ) {
                $this->setError("Move would split hive");
            } elseif (isset($this->board[$to]) && $tile[1] != "B") {
                $this->setError("Tile not empty");
            } elseif ( ($tile[1] == "Q" || $tile[1] == "B")
             && !slide($this->board,$from, $to))  {
                $this->setError("Tile must slide");
            } else {
                return $tile;
            }
        }
        return false;
    }
    public function undo()
    {
        // Game logic for undoing
        $result = $this->dbHandler->undoSet();
        $_SESSION['last_move'] = $result[5];
        list($a, $b, $c) = unserialize($result[6]);
        print_r( $result[5]);
        print_r( $result[6]);
        // $_SESSION['board'] = $a;
        // $_SESSION['hand'] = $b;
        // $_SESSION['player'] = $c;

        // $this->updateGameState();
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
?>