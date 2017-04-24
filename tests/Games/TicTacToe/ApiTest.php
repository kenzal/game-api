<?php

namespace Tests\Games\TicTacToe;

use Games\TicTacToe\Api;
use Games\TicTacToe\GameState;
use Games\TicTacToe\Interfaces\EngineInterface;
use PHPUnit\Framework\TestCase;
use Tests\Traits\Faker;
use Games\TicTacToe\Move;

/**
 * PHPUnit Test covering the Tic-Tac-Toe API Class
 *
 * @covers \Games\TicTacToe\Api
 */
final class ApiTest extends TestCase
{
    use Faker;

    /**
     * Mock Game Engine
     *
     * @var EngineInterface
     */
    protected $mockEngine;

    /**
     * Stubbed Api for overwriting constructor and getEngine() method
     *
     * @var Api
     */
    protected $stubbedApi;

    /**
     * Pre-test Set Up
     *
     * Initializes various properties for use in testing
     */
    public function setUp()
    {
        $this->mockEngine = $this->createMock(EngineInterface::class);
        $this->stubbedApi = $this->getMockBuilder(Api::class)
                                 ->disableOriginalConstructor()
                                 ->setMethods(['getEngine'])
                                 ->getMock();

        $this->stubbedApi->expects($this->any())
             ->method('getEngine')
             ->willReturn($this->mockEngine);
    }

    /**
     * Tests that initizalition fails with Exception thrown for bad arguments
     *
     * @param string $argument argument to pass to constructor
     *
     * @dataProvider badEngineClassProvider
     */
    public function testInitializeWithBadClassFails(string $argument)
    {
        $this->expectException(\InvalidArgumentException::class);
        new Api($argument);
    }

    /**
     * Test that the Api can be instanciated with mock engine
     */
    public function testWithMockEngine()
    {
        $api = new Api(get_class($this->mockEngine));
        $game = $this->createMock(GameState::class);
        $this->assertInstanceOf(Api::class, $api);
        $this->assertInstanceOf(EngineInterface::class, $api->getEngine($game));
    }

    /**
     * Tests the makeMove() method
     *
     * @param Move   $move        Move to make
     * @param string $playerToken character representing the player
     *
     * @dataProvider moveProvider
     */
    public function testMakeMove(?Move $move, $playerToken)
    {
        $board = [['', '', ''], ['', '', ''], ['', '', '']];

        $this->mockEngine->expects($this->any())
             ->method('getMove')
             ->willReturn($move);

        if (!$move) {
            $this->assertNull($this->stubbedApi->makeMove($board, $playerToken));
            return;
        }
        $this->assertEquals(
            [
                $move->getX(),
                $move->getY(),
                $playerToken
            ],
            $this->stubbedApi->makeMove($board, $playerToken)
        );
    }

    /**
     * Tests the getWinner() method
     *
     * @param array $boardstate board state
     * @param mixed $expected   expected result
     *
     * @dataProvider getWinnerProvider
     */
    public function testGetWinner(array $boardstate, $expected)
    {
        $this->assertSame($expected, $this->stubbedApi->getWinner($boardstate));
    }


    /**
     * Provider - Bad Engine Classes
     *
     * @return array[] Array of arguments arrays - arguments are:
     *                     sting class names
     */
    public function badEngineClassProvider()
    {
        return [
            'Test Class'               => [self::class],
            'Random Class'             => [\DateTime::class],
            'Non-Instantionable Class' => [Engine::class],
        ];
    }

    /**
     * Provider - getWinner
     *
     * @return array[] Array of arguments arrays - arguments are:
     *                      string[][] single character two-dimensional array board state
     *                      mixed      expected value
     */
    public function getWinnerProvider()
    {
        return [
            'X-Win'      => [[['X', 'X', 'X'], ['O', 'O', '' ], ['' , '' , '' ]], 'X'],
            'O-Win'      => [[['X', 'X', '' ], ['O', 'O', 'O'], ['X', '' , '' ]], 'O'],
            '1-Win'      => [[['1', '1', '1'], ['2', '2', '' ], ['' , '' , '' ]], '1'],
            'Unfinished' => [[['X', 'X', '' ], ['O', 'O', '' ], ['' , '' , '' ]], null],
            'Draw'       => [[['X', 'X', 'O'], ['O', 'O', 'X'], ['X', 'O', 'X']], false],
            'New'        => [[['' , '' , '' ], ['' , '' , '' ], ['' , '' , '' ]], null],
        ];
    }

    /**
     * Provider - Moves
     *
     * @return array[] Array of arguments arrays - arguments are:
     *                      Move Game Move
     */
    public function moveProvider()
    {
        $this->setUpFaker(); //Must be called explicitly in providers
        $moves = ['endgame' => [null, 'X']];
        $game = $this->createMock(GameState::class);
        foreach (range(0, 2) as $row) {
            foreach (range(0, 2) as $col) {
                $token    = $this->getFaker()->randomLetter();
                $tokens[] = $token;
                $moves["loc({$row},{$col})"] = [new Move($game, $row, $col), $token];
            }
        }

        $game->method('getTurnToMove')
             ->will(new \PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls($tokens));
        return $moves;
    }
}
