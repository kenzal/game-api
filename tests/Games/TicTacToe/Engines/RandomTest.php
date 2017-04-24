<?php

namespace Tests\Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Engines\Random;
use Games\TicTacToe\Interfaces\EngineInterface;
use PHPUnit\Framework\TestCase;
use Tests\Traits\Faker;

/**
 * @covers \Games\TicTacToe\Engines\Random
 */
final class RandomTest extends TestCase
{
    use Faker;

    /** @var GameState */
    protected $mockState;

    public function setUp()
    {
        $this->mockState = $this->createMock(GameState::class);
    }

    public function testCanBeCreated()
    {
        $engine = new Random($this->mockState);
        $this->assertInstanceOf(EngineInterface::class, $engine);
        $this->assertInstanceOf(Random::class, $engine);
    }

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
