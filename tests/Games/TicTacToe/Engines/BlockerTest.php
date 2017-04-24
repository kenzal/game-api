<?php

namespace Tests\Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Move;
use Games\TicTacToe\Engines\Blocker;
use Games\TicTacToe\Interfaces\EngineInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Games\TicTacToe\Engines\Blocker
 */
final class BlockerTest extends TestCase
{
    /** @var GameState */
    protected $mockState;

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

    public function testCanBeCreated()
    {
        $engine = new Blocker($this->mockState);
        $this->assertInstanceOf(EngineInterface::class, $engine);
        $this->assertInstanceOf(Blocker::class, $engine);
    }

    public function testMockCanBeCreated()
    {
        $this->assertInstanceOf(EngineInterface::class, $this->mockEngine);
        $this->assertInstanceOf(Blocker::class, $this->mockEngine);
    }

    public function testGetConsideredMovesReturnsFromConsideredWithZeroBlockSolutions()
    {
        $this->nextState->expects($this->exactly(count($this->moves)))
             ->method('isEndGame')
             ->willReturn(false);

        $this->assertSame($this->moves, $this->mockEngine->getConsideredMoves());
    }

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

    public function testIntegrationSelectBlockOverWin()
    {
        $state     = GameState::CreateFromString('X OX O   ', 'O');
        $winMove   = [2, 2, 'O'];
        $blockMove = [0, 2, 'O'];
        $validMoves = $state->getValidMoves();

        $this->assertContains($winMove, $this->movesToArray($validMoves));
        $this->assertContains($blockMove, $this->movesToArray($validMoves));

        $engine = new Blocker($state);
        $consideredMoves = $engine->getConsideredMoves();

        $this->assertNotContains($winMove, $this->movesToArray($consideredMoves));
        $this->assertContains($blockMove, $this->movesToArray($consideredMoves));
    }

    protected function movesToArray(array $moves)
    {
        return array_map(function (Move $move) {
            return $move->asArray();
        }, $moves);
    }
}
