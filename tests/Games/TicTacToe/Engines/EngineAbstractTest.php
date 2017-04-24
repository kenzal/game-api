<?php
namespace Tests\Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Engines\EngineAbstract;
use Games\TicTacToe\Interfaces\EngineInterface;
use PHPUnit\Framework\TestCase;
use Tests\Traits\Faker;

/**
 * PHPUnit test to cover abstract Tic-Tac-Toe Engine
 *
 * @covers \Games\TicTacToe\Engines\EngineAbstract
 */
final class EngineAbstractTest extends TestCase
{
    use Faker;

    /**
     * Mock Game State
     *
     * @var GameState
     */
    protected $mockState;

    /**
     * Mock Game Engine created from the abstract
     *
     * @var EngineAbstract
     */
    protected $mockEngine;

    /**
     * Pre-test Set Up
     *
     * Initializes various properties for use in testing
     */
    public function setUp()
    {
        $this->mockState = $this->createMock(GameState::class);

        $this->mockEngine = $this->getMockForAbstractClass(EngineAbstract::class, [$this->mockState]);
    }

    /**
     * Tests class constructor
     */
    public function testCanBeCreated()
    {
        $this->assertInstanceOf(EngineAbstract::class, $this->mockEngine);
        $this->assertInstanceOf(EngineInterface::class, $this->mockEngine);
    }

    /**
     * Tests basic EngineAbstract::getMove() functionality
     *
     * Tests that EngineAbstract::getMove() should always return a
     * single element returned by EngineAbstract::getConsideredMoves()
     */
    public function testGetMoveReturnsFromConsidered()
    {
        $testOptions = $this->getFaker()->words(10, false);
        $this->mockEngine->expects($this->once())
             ->method('getConsideredMoves')
             ->will($this->returnValue($testOptions));
        $this->assertContains($this->mockEngine->getMove(), $testOptions);
    }
}
