<?php
use PHPUnit\Framework\TestCase;

class HiveAITest extends TestCase
{
    private $dbHandlerMock;
    private $game;
    private $hiveAiMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock mysqli
        $mysqliMock = $this->getMockBuilder(mysqli::class)
        ->disableOriginalConstructor()
        ->getMock();

        // Mock DbHandler
        $this->dbHandlerMock = $this->getMockBuilder(DbHandler::class)
            ->setConstructorArgs([$mysqliMock])
            ->getMock();

        // Mock the getGameMoves method of DbHandler
        $this->dbHandlerMock->expects($this->any())
        ->method('getGameMoves')
        ->with($_SESSION['game_id'])
        ->willReturn(  (object) [ "num_rows" => 2  ]);

        // Mock HiveAI
        $this->hiveAiMock = $this->getMockBuilder(HiveAI::class)
        ->disableOriginalConstructor()
        ->getMock();

        // Pass the mock DbHandler to the Game constructor
        $this->game = new Game($this->dbHandlerMock, $this->hiveAiMock);
        $this->game->restart();
    }

    public function testAIPlaysTheFirstMoveReturnArrayHasKeyTrue(): void
    {
        $this->hiveAiMock->expects($this->once())
        ->method('suggestMove')
        ->willReturn(["play", "Q", "0,0"]);


        $this->game->AI();

        $this->assertArrayHasKey('0,0',  $this->game->board);
    }

    public function testAIPlaysTheSecondMoveReturnArrayHasKeyTrue(): void
    {
        $this->game->play("Q", "0,0");

        $this->hiveAiMock->expects($this->once())
        ->method('suggestMove')
        ->willReturn(["play", "Q", "1,0"]);


        $this->game->AI();

        $this->assertArrayHasKey('1,0',  $this->game->board);
    }
    public function testAIMovesAPieceReturnArrayHasKeyTrue(): void
    {
        $this->game->play("Q", "0,0");
        $this->game->play("Q", "1,0");

        $this->hiveAiMock->expects($this->once())
        ->method('suggestMove')
        ->willReturn(["move", "0,0", "0,1"]);


        $this->game->AI();

        $this->assertArrayHasKey('0,1',  $this->game->board);
    }

    public function testAIMovesGiveBackEmptyResultReturnCountTwo(): void
    {
        $this->game->play("Q", "0,0");
        $this->game->play("Q", "1,0");

        $this->hiveAiMock->expects($this->once())
        ->method('suggestMove')
        ->willReturn(null);


        $this->game->AI();
        $this->assertCount(2,  $this->game->board);
    }

    public function testAIPassLengthOfBoardStaysTheSameReturnCountTwo(): void
    {
        $this->game->play("Q", "0,0");
        $this->game->play("Q", "1,0");

        $this->hiveAiMock->expects($this->once())
        ->method('suggestMove')
        ->willReturn(["pass", null, null]);


        $this->game->AI();
        $this->assertCount(2,  $this->game->board);
    }
    
}
