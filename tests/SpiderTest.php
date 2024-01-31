<?php
use PHPUnit\Framework\TestCase;

class SpiderTest extends TestCase
{
 
    public function testSpiderMove3TimesOverNotEmptyTilesReturnsFalse()
    {
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "Q" ]],
            "0,-1" => [[0, "S" ]],
        ];
    
        $this->assertFalse(spiderMove('0,-1', '0,2', $board));
    }
    
    public function testSpiderMoveMoves4TimesReturnsFalse()
    {
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "Q" ]],
            "0,-1" => [[0, "S" ]],
            "0,2" => [[1, "A" ]],
        ];
    
        $this->assertFalse(spiderMove('0,-1', '-1,3', $board));
    }

    public function testSpiderMoveMoves2TimesReturnsFalse()
    {
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "Q" ]],
            "0,-1" => [[0, "S" ]],
            "0,2" => [[1, "A" ]],
        ];
    
        $this->assertFalse(spiderMove('0,-1', '-1,1', $board));
    }
    
    public function testSpiderMove3TimesOverEmptyTilesReturnsTrue()
    {
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "Q" ]],
            "0,-1" => [[0, "S" ]],
            "0,2" => [[1, "A" ]],
        ];
    
        $this->assertTrue(spiderMove('0,-1', '1,1', $board));
    }
    
    public function testSpiderMoveBackToOriginalPlaceReturnsFalse()
    {
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "Q" ]],
            "0,-1" => [[0, "S" ]],
            "0,2" => [[1, "A" ]],
        ];
    
        $this->assertFalse(spiderMove('0,-1', '0,-1', $board));
    }
    
    public function testSpiderMoveBacktrackingReturnsFalse()
    {
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "Q" ]],
            "0,-1" => [[0, "S" ]],
            "0,2" => [[1, "A" ]],
        ];
        
        $this->assertFalse(spiderMove('0,-1', '0,1', $board));
    }
   

    
}
