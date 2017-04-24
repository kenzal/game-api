<?php
namespace Tests\Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Engines\EngineAbstract;
use Games\TicTacToe\Interfaces\EngineInterface;
use PHPUnit\Framework\TestCase;
use Tests\Traits\Faker;

/**
 * @covers \Games\TicTacToe\Engines\EngineAbstract
 */
final class EngineAbstractTest extends TestCase
{
    use Faker;

    /** @var GameState */
    protected $mockState;

    /** @var Engine */
    protected $mockEngine;

    public function setUp()
    {
        $this->mockState = $this->createMock(GameState::class);

        $this->mockEngine = $this->getMockForAbstractClass(EngineAbstract::class, [$this->mockState]);
        // $stub->expects($this->any())
        //      ->method('abstractMethod')
        //      ->will($this->returnValue(TRUE));
    }

    public function testCanBeCreated()
    {
        $this->assertInstanceOf(EngineAbstract::class, $this->mockEngine);
        $this->assertInstanceOf(EngineInterface::class, $this->mockEngine);
    }

    public function testGetMoveReturnsFromConsidered()
    {
        $testOptions = $this->getFaker()->words(10, false);
        $this->mockEngine->expects($this->once())
             ->method('getConsideredMoves')
             ->will($this->returnValue($testOptions));
        $this->assertContains($this->mockEngine->getMove(), $testOptions);
    }
}
