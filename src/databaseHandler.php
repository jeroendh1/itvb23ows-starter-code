<?php

class DbHandler
{
    private $db;

    const MOVES_TABLE = 'moves';
    const GAMES_TABLE = 'games';

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function saveMove($gameId, $type, $from, $to, $lastMove, $state)
    {        
        $stmt = $this->db->prepare('INSERT INTO ' . self::MOVES_TABLE . ' (game_id, type, move_from, move_to, previous_id, state) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isssis', $gameId, $type, $from, $to, $lastMove, $state);
        $stmt->execute();

        return $this->db->insert_id;
    }
    public function getGameMoves($gameId) {
        $stmt = $this->db->prepare('SELECT * FROM moves WHERE game_id = ?');
        $stmt->bind_param('i', $gameId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result;
    }

    public function createNewGame() {   
        $this->db->prepare('INSERT INTO games VALUES ()')->execute();
        return $this->db->insert_id;
    }

    public function getMove($id){
        $stmt = $this->db->prepare('SELECT * FROM moves WHERE id = ? and game_id = ?');
        $stmt->bind_param('ii', $id, $_SESSION['game_id']);
        $stmt->execute();
       
        return $stmt->get_result()->fetch_array();
    }
    
    public function deleteMove($id){
        $stmt = $this->db->prepare('DELETE FROM moves WHERE id = ?');
        $stmt->bind_param('i',$id);
        $stmt->execute();
    }

    public function saveGame()
    {
        $stmt = $this->db->prepare('INSERT INTO ' . self::GAMES_TABLE . ' VALUES ()');
        $stmt->execute();
        return $this->db->insert_id;
    }

    // public function loadPreviousState($moveId)
    // {
    //     // Load previous state from the database
    //     // ...
    // }
}
?>
