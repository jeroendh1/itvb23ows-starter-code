<?php 
use PHPUnit\Framework\TestCase;

class WinGameTest extends TestCase
{
 
    public function testHasLostGameNoQueenPlayedReturnsFalse()
    {
        $board = [
            "0,0" => [[0, "S"]],
            "0,1" => [[1, "A" ]],
            "0,-1" => [[0, "S" ]],
        ];
    
        $this->assertFalse(hasLostGame($board, 1));
    }
    
    public function testHasLostGameQueenIsSurroundedReturnsTrue()
    {
        $board = [
            "1,0" => [[1, 'S']],
            "-1,0" => [[1, 'S']],
            "-1,1" => [[1, 'G']],
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, 'A']],
            "0,-1" => [[1, 'Q']],
            "1,-1" => [[1, 'B']],
        ];
    
        $this->assertTrue(hasLostGame($board,0));
    } 
    public function testHasLostGameQueenIsAlmostSurroundedReturnsFalse()
    {
        $board = [
            "-1,0" => [[1, 'S']],
            "-1,1" => [[1, 'G']],
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, 'A']],
            "0,-1" => [[1, 'Q']],
            "1,-1" => [[1, 'B']],
        ];
    
        $this->assertFalse(hasLostGame($board,0));
    }  


    public function testHasLostGameQueenBeeNotOwnedByPlayerReturnFalse(): void
    {
        $board = [
            '0,0' => [[1, 'Q']],
            '0,1' => [[1, 'B']],
            '1,0' => [[1, 'B']],
            '1,1' => [[0, 'B']]
        ];
        
        $this->assertFalse(hasLostGame($board, 0));
    }
    
}
