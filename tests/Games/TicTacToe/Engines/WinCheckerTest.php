<?php

namespace Tests\Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Move;
use Games\TicTacToe\Engines\WinChecker;
use Games\TicTacToe\Interfaces\EngineInterface;
use PHPUnit\Framework\TestCase;

/**
 * PHPUnit test to cover WinChecker Tic-Tac-Toe Engine
 *
 * @covers \Games\TicTacToe\Engines\WinChecker
 */
final class WinCheckerTest extends TestCase
{
    /**
     * Mock Game State
     *
     * @var GameState
     */
    protected $mockState;

    /**
     * Game Engine using the mock game state
     *
     * @var WinChecker
     */
    protected $engine;

    /**
     * Array of Moves
     *
     * @var Move[]
     */
    protected $moves;

    /**
     * Pre-test Set Up
     *
     * Initializes various properties for use in testing
     */
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

    /**
     * Tests class constructor
     */
    public function testCanBeCreated()
    {
        $this->assertInstanceOf(EngineInterface::class, $this->engine);
        $this->assertInstanceOf(WinChecker::class, $this->engine);
    }

    /**
     * Tests WinChecker::getConsideredMoves() functionality with zero winning solutions
     *
     * Verifies that WinChecker::getConsideredMoves() should return the full
     * contents of GameState::getValidMoves() when no winning solutions can be found
     */
    public function testGetConsideredMovesReturnsFromConsideredWithZeroWinSolutions()
    {
        $this->mockState->expects($this->exactly(count($this->moves)))
             ->method('isEndGame')
             ->willReturn(false);

        $this->assertSame($this->moves, $this->engine->getConsideredMoves());
    }

    /**
     * Tests WinChecker::getConsideredMoves() functionality with one winning solution
     *
     * Verifies that WinChecker::getConsideredMoves() should return only the
     * winning move when exactly one winning solution can be found
     */
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

    /**
     * Tests WinChecker::getConsideredMoves() functionality with multiple winning solutions
     *
     * Verifies that WinChecker::getConsideredMoves() should return all of and
     * only the winning moves when no multiple winning solutions can be found
     */
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
