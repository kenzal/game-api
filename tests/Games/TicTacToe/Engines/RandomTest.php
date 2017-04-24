<?php

namespace Tests\Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Engines\Random;
use Games\TicTacToe\Interfaces\EngineInterface;
use PHPUnit\Framework\TestCase;
use Tests\Traits\Faker;

/**
 * PHPUnit test to cover Random Tic-Tac-Toe Engine
 *
 * @covers \Games\TicTacToe\Engines\Random
 */
final class RandomTest extends TestCase
{
    use Faker;

    /**
     * Mock Game State
     *
     * @var GameState
     */
    protected $mockState;

    /**
     * Pre-test Set Up
     *
     * Initializes various properties for use in testing
     */
    public function setUp()
    {
        $this->mockState = $this->createMock(GameState::class);
    }

    /**
     * Tests class constructor
     */
    public function testCanBeCreated()
    {
        $engine = new Random($this->mockState);
        $this->assertInstanceOf(EngineInterface::class, $engine);
        $this->assertInstanceOf(Random::class, $engine);
    }

    /**
     * Tests basic Random::getConsideredMoves() functionality
     *
     * Verifies that Random::getConsideredMoves() returns the full set
     * from GameState::getValidMoves() for the provided game state
     */
    public function testGetConsideredMovesReturnsFromConsidered()
    {
        $engine = new Random($this->mockState);
        $testOptions = $this->getFaker()->words(10, false);
        $this->mockState->expects($this->once())
             ->method('getValidMoves')
             ->will($this->returnValue($testOptions));
        $this->assertSame($testOptions, $engine->getConsideredMoves());
    }
}
