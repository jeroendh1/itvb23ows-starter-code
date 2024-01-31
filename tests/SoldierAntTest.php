<?php 
use PHPUnit\Framework\TestCase;

class SoldierAntTest extends TestCase
{
    private $dbHandlerMock;
    private $game; 
    public function setUp(): void {
        parent::setUp();

        // Mock mysqli
        $mysqliMock = $this->getMockBuilder(mysqli::class)
        ->disableOriginalConstructor()
        ->getMock();

        // Mock DbHandler
        $this->dbHandlerMock = $this->getMockBuilder(DbHandler::class)
            ->setConstructorArgs([$mysqliMock])
            ->getMock();

        // Pass the mock DbHandler to the Game constructor
        $this->game = new Game($this->dbHandlerMock , new HiveAI);
        $this->game->restart();
    }
    
    public function testSoldierAntMoveToOppositPositionReturnsTrue()
    {
        $board = [
            "0,0" => [[0, "A"]],
            "0,1" => [[1, "Q" ]],
            "0,-1" => [[0, "Q" ]],
            "0,2" => [[1, "B" ]],
            "0,-2" => [[0, "A"]]
        ];
    
        $this->assertTrue(SoldierAntMove('0,-2', '-1,3', $board));
    }
    
    public function testSoldierAntMoveToNextRightPositionReturnsTrue()
    {
        $board = [
            "0,0" => [[0, "A"]],
            "0,1" => [[1, "Q" ]],
            "0,-1" => [[0, "Q" ]],
            "0,2" => [[1, "B" ]],
            "0,-2" => [[0, "A"]]
        ];
    
        $this->assertTrue(SoldierAntMove('0,-2', '1,-2', $board));
    }    
    
    public function testSoldierAntMoveToNotEmptyPossitionnReturnsFalse()
    {
        
        $this->game->board = [
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']],
            '-1,0' => [[0, 'A']],
            '0,2' => [[1, 'A']]
        ];
        $this->game->player = 1;
        $this->game->hand=  [1 => ['B' => 2, 'S' => 2, 'A' => 1, 'G' => 3]];



        $this->assertFalse($this->game->validateMove('-1,0', '0,2'));
    }

    public function testSoldierAntMoveToCurrentPossitionnReturnsFalse()
    {
    
        $this->game->board = [
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']],
            '-1,0' => [[0, 'A']],
            '0,2' => [[1, 'A']]
        ];
        $this->game->player = 1;
        $this->game->hand=  [1 => ['B' => 2, 'S' => 2, 'A' => 1, 'G' => 3]];

        $this->assertFalse($this->game->validateMove('-1,0', '-1,0'));
    }

   

    
}
