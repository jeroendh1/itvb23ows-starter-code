<?php 
use PHPUnit\Framework\TestCase;
// (0,0)
//      0,-3     1,-3    2,-3
// -1,-2     0,-2    1,-2    2,-2   
//     -1,-1     0,-1    1,-1
// -2,0     -1,0     0,0     1,0
//     -2,1     -1,1     0,1    1,1 
// 
// -1,0 0,-1 -1,1

// 
class GameTest extends TestCase
{
    private $db;
    private $dbHandler;
    private $game; 
    public function setUp(): void {
        
        $this->db = new mysqli('localhost:9906', 'root', '', 'hive');
        $this->dbHandler  = new DbHandler($this->db);
        $this->game = new Game($this->dbHandler);
        $this->game->restart();
    }
    
    public function testValidatePositionEmptyBoard(): void
    {
        // Test a position on an empty board
        $result = $this->game->validatePosition('0,0');

        $this->assertTrue($result);
    }
    public function testValidatePositionWherePositionIsFull(): void
    {

        $this->game->board = [
            '0,0' => [[0, 'Q']],
        ];
        $this->game->player = 1;
        $this->game->hand=  [1 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]];

        // Test a position thats not empty
        $result = $this->game->validatePosition('0,0');

        $this->assertFalse($result);
    }

    public function testValidatePositionWithNeighbor()
    {

        $this->game->board = [
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']]
        ];
        $this->game->player = 0;
        $this->game->hand=  [0 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]];

        // Test a position with a neighbor
        $result = $this->game->validatePosition('-0,1');
        $this->assertTrue($result);
    }

    
    public function testValidatePositionWithDifferentColorNeighbor()
    {
  
        $this->game->board = [
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']]
        ];
        $this->game->player = 1;
        $this->game->hand=  [1 => ['Q' => 1, 'B' => 1, 'S' => 2, 'A' => 3, 'G' => 3]];

        // Test a position with a neighbor of a different color
        $result = $this->game->validatePosition('-0,1');
        $this->assertFalse($result);
    }

    public function testGetPossiblePositionsWithEmptyBoard(){
      
         // Act
         $result = $this->game->getPossiblePositions();

         // Assert
         $this->assertContains('0,0', $result);
         $this->assertCount(1, $result);
    }

    public function testGetPossiblePositionsWithOnePieceOnBoard(){
        $this->game->board = [
            '0,0' => [[0, 'Q']],
        ];
        $this->game->player = 1;
        $this->game->hand=  [1 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]];
        // Act
        $result = $this->game->getPossiblePositions();

        // Assert
        $this->assertContains('0,1', $result);
        $this->assertContains('0,-1', $result);
        $this->assertContains('-1,0', $result);
        $this->assertContains('1,0', $result);
        $this->assertContains('-1,1', $result);
        $this->assertContains('1,-1', $result);
        $this->assertCount(6, $result);
   }
   public function testGetPlayerPositionsWith7PositionsReturnsArray()
    {
    
        $this->game->board  = [
            '0, 0' => [[0, 'Q']],
            '0, 1' => [[1, 'Q']],
            '0, 2' => [[1, 'B']],
            '-1, 0' => [[0, 'B']],
            '0, 3' => [[1, 'B']],
            '0, -1' => [[0, 'B']],
            '0, 4' => [[1, 'S']],
        ];
        $this->game->player = 1;
        $this->game->hand=  [1 => ['Q' => 1, 'B' => 1, 'S' => 1, 'A' => 1, 'G' => 3]];
        // Act
        $result = $this->game->getPlayerPositions();

        // Assert
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result); 
    }
// ==========================================================================
//                          test bug 2
    public function testLegalMoveQueenToOpponentNeighbor()
    {
        // Set up the game state
        $this->game->board = [
            '0,0' => [[0, 'Q']],  // White queen at (0, 0)
            '1,0' => [[1, 'Q']]   // Black queen at (1, 0)
        ];
        $this->game->player = 0;
        $this->game->hand = [0 => ['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]];

        $result = $this->game->validateMove('0,0', '0,1');

        // Assert
        $this->assertTrue($result);
    }


}
   

?>