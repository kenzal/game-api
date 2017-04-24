<?php

namespace Tests\Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Move;
use Games\TicTacToe\Engines\WinChecker;
use Games\TicTacToe\Interfaces\EngineInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Games\TicTacToe\Engines\WinChecker
 */
final class WinCheckerTest extends TestCase
{
    /** @var GameState */
    protected $mockState;

    public function setUp()
    {
        $this->mockState = $this->createMock(GameState::class);
        $this->engine = new WinChecker($this->mockState);

        $this->moves = [];
        foreach (range(0, 3) as $xPos) {
            foreach (range(0, 3) as $yPos) {
                $this->moves[] = $this->createMock(Move::class, [$this->mockState, $xPos, $yPos]);
            }
        }

        $this->mockState->expects($this->any())
             ->method('getValidMoves')
             ->will($this->returnValue($this->moves));
        $this->mockState->expects($this->any())
             ->method('makeMove')
             ->will($this->returnSelf());
    }

    public function testCanBeCreated()
    {
        $this->assertInstanceOf(EngineInterface::class, $this->engine);
        $this->assertInstanceOf(WinChecker::class, $this->engine);
    }

    public function testGetConsideredMovesReturnsFromConsideredWithZeroWinSolutions()
    {
        $this->mockState->expects($this->exactly(count($this->moves)))
             ->method('isEndGame')
             ->willReturn(false);

        $this->assertSame($this->moves, $this->engine->getConsideredMoves());
    }

    public function testGetConsideredMovesReturnsCorrectMoveFromConsideredWithOneWinSolution()
    {
        //Return True only on $this->moves[2]
        $this->mockState->expects($this->exactly(count($this->moves)))
             ->method('isEndGame')
             ->will($this->onConsecutiveCalls(false, false, true, false, false, false, false, false, false));

        $consideredMoves = $this->engine->getConsideredMoves();
        $this->assertNotContains($this->moves[0], $consideredMoves);
        $this->assertContains($this->moves[2], $consideredMoves);
        $this->assertCount(1, $consideredMoves);
    }

    public function testGetConsideredMovesReturnsCorrectMoveFromConsideredWithMultipleWinSolutions()
    {
        //Return True only on $this->moves[2], $this->moves[4]
        $this->mockState->expects($this->exactly(count($this->moves)))
             ->method('isEndGame')
             ->will($this->onConsecutiveCalls(false, false, true, false, true, false, false, false, false));

        $consideredMoves = $this->engine->getConsideredMoves();
        $this->assertNotContains($this->moves[0], $consideredMoves);
        $this->assertContains($this->moves[2], $consideredMoves);
        $this->assertContains($this->moves[4], $consideredMoves);
        $this->assertCount(2, $consideredMoves);
    }
}
