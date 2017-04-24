<?php

namespace Tests\Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Move;
use Games\TicTacToe\Engines\Blocker;
use Games\TicTacToe\Interfaces\EngineInterface;
use PHPUnit\Framework\TestCase;

/**
 * PHPUnit test to cover Blocker Tic-Tac-Toe Engine
 *
 * @covers \Games\TicTacToe\Engines\Blocker
 */
final class BlockerTest extends TestCase
{
    /**
     * Mock Game State
     *
     * @var GameState
     */
    protected $mockState;

    /**
     * Mock Game State for "Next Game"
     *
     * @var GameState
     */
    protected $nextState;

    /**
     * Stubbed Engine (for overriding Blocker::getNextState())
     *
     * @var Blocker
     */
    protected $mockEngine;

    /**
     * Moves Array
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
        $this->nextState = $this->createMock(GameState::class);
        $this->mockEngine = $this->getMockBuilder(Blocker::class)
                                 ->setConstructorArgs([$this->mockState])
                                 ->setMethods(['getNextState'])
                                 ->getMock();
        $this->mockEngine->expects($this->any())
                         ->method('getNextState')
                         ->willReturn($this->nextState);

        $this->moves = [];
        foreach (range(0, 3) as $xPos) {
            foreach (range(0, 3) as $yPos) {
                $this->moves[] = $this->createMock(Move::class, [$this->mockState, $xPos, $yPos]);
            }
        }

        $this->mockState->expects($this->any())
             ->method('getValidMoves')
             ->will($this->returnValue($this->moves));


        $this->nextState->expects($this->any())
             ->method('makeMove')
             ->will($this->returnSelf());
    }

    /**
     * Tests class constructor
     */
    public function testCanBeCreated()
    {
        $engine = new Blocker($this->mockState);
        $this->assertInstanceOf(EngineInterface::class, $engine);
        $this->assertInstanceOf(Blocker::class, $engine);
    }

    /**
     * Tests that class can be constructed with mock engine and that engine is set up
     */
    public function testMockCanBeCreated()
    {
        $this->assertInstanceOf(EngineInterface::class, $this->mockEngine);
        $this->assertInstanceOf(Blocker::class, $this->mockEngine);
    }

    /**
     * Tests that all moves are returned when there are no block solutions
     */
    public function testGetConsideredMovesReturnsFromConsideredWithZeroBlockSolutions()
    {
        $this->nextState->expects($this->exactly(count($this->moves)))
             ->method('isEndGame')
             ->willReturn(false);

        $this->assertSame($this->moves, $this->mockEngine->getConsideredMoves());
    }

    /**
     * Tests that one blocking move is returned when there is exactly one block solution
     */
    public function testGetConsideredMovesReturnsCorrectMoveFromConsideredWithOneBlockSolution()
    {
        //Return True only on $this->moves[2]
        $this->nextState->expects($this->exactly(count($this->moves)))
             ->method('isEndGame')
             ->will($this->onConsecutiveCalls(false, false, true, false, false, false, false, false, false));

        $consideredMoves = $this->mockEngine->getConsideredMoves();
        $this->assertNotContains($this->moves[0], $consideredMoves);
        $this->assertContains($this->moves[2], $consideredMoves);
        $this->assertCount(1, $consideredMoves);
    }

    /**
     * Tests that all and only blocking moves are returned when there are multiple block solutions
     */
    public function testGetConsideredMovesReturnsCorrectMoveFromConsideredWithMultipleBlockSolutions()
    {
        //Return True only on $this->moves[2], $this->moves[4]
        $this->nextState->expects($this->exactly(count($this->moves)))
             ->method('isEndGame')
             ->will($this->onConsecutiveCalls(false, false, true, false, true, false, false, false, false));

        $consideredMoves = $this->mockEngine->getConsideredMoves();
        $this->assertNotContains($this->moves[0], $consideredMoves);
        $this->assertContains($this->moves[2], $consideredMoves);
        $this->assertContains($this->moves[4], $consideredMoves);
        $this->assertCount(2, $consideredMoves);
    }

    /**
     * Verifies that a the engine will select a blocking move over a winning one
     */
    public function testIntegrationSelectBlockOverWin()
    {
        $state     = GameState::CreateFromString('X OX O   ', 'O');
        $winMove   = [2, 2, 'O'];
        $blockMove = [0, 2, 'O'];
        $validMoves = $state->getValidMoves();

        $this->assertContains($winMove, $this->movesToArray($validMoves));
        $this->assertContains($blockMove, $this->movesToArray($validMoves));

        $engine          = new Blocker($state);
        $consideredMoves = $engine->getConsideredMoves();

        $this->assertNotContains($winMove, $this->movesToArray($consideredMoves));
        $this->assertContains($blockMove, $this->movesToArray($consideredMoves));
    }

    /**
     * Utility method to get an array of movement arrays from an array of Move objects
     *
     * @param Move[]  $moves array of Move objects
     *
     * @return array[] Move::asArray() mapped to each element of $moves
     */
    protected function movesToArray(array $moves)
    {
        return array_map(
            function (Move $move) {
                return $move->asArray();
            },
            $moves
        );
    }
}
