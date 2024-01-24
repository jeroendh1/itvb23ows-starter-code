<?php 
use PHPUnit\Framework\TestCase;

class GrasshopperTest extends TestCase
{
    public function testValidHorizontalMoveReturnsTrue()
    {
        // Set up the game state
        $board = [
            '0,0' => [[0, 'Q']],  // White player
            '0,1' => [[1, 'Q']],  // Black player 
            '-1,0' => [[0, 'G']],  // White player
            '0,2' => [[1, 'G']]
        ];

        $this->assertTrue(GrasshopperMove('-1,0', '1,0', $board));
    }

    public function testValidDiagonalToRightMoveReturnsTrue()
    {
        $board = [
            '0,0' => [[0, 'Q']],  // White player
            '0,1' => [[1, 'Q']],  // Black player 
            '-1,0' => [[0, 'S']],  // White player
            '0,2' => [[1, 'S']],
            '-1,-1' => [[0, 'G']],  // White player
            '0,3' => [[1, 'S']],
        ];

        $this->assertTrue(GrasshopperMove('-1,-1', '-1,1', $board));
    }

    public function testValidDiagonalToLeftMoveReturnsTrue()
    {
        $board = [
            '0,0' => [[0, 'Q']],  // White player
            '0,1' => [[1, 'Q']],  // Black player 
            '1,-1' => [[0, 'G']],  // White player
            '-1,2' => [[1, 'G']]
        ];

        $this->assertTrue(GrasshopperMove('1,-1', '-1,1', $board));
    }

    public function testInvalidMoveReturnsFalse()
    {
        // Set up the game state
        $board = [
            '0,0' => [[0, 'Q']],  // White player
            '0,1' => [[1, 'Q']],  // Black player 
            '-1,0' => [[0, 'G']],  // White player
            '0,2' => [[1, 'G']]
        ];

        // Attempt an invalid move
        $this->assertFalse(GrasshopperMove('-1,0', '0,3', $board));
    }


    public function testInvalidMoveWithNoObstacleReturnsFalse()
    {
        // Set up the game state
        $board = [
            '0,0' => [[0, 'Q']],  // White player
            '0,1' => [[1, 'Q']],  // Black player 
            '-1,0' => [[0, 'G']],  // White player
            '0,2' => [[1, 'G']]
        ];

        // Attempt an invalid diagonal move
        $this->assertFalse(GrasshopperMove('-1,0', '-1,1', $board));
    }

    public function testInvalidMoveToSplitHiveReturnsFalse()
    {
        // Set up the game state
        $board = [
            '0,0' => [[0, 'Q']],  // White player
            '0,1' => [[1, 'Q']],  // Black player 
            '-1,0' => [[0, 'G']],  // White player
            '0,2' => [[1, 'G']]
        ];

        // Attempt an invalid diagonal move
        $this->assertFalse(GrasshopperMove('-1,0', '-2,0', $board));
    }

    
}
