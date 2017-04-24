<?php

namespace Tests\Games\TicTacToe;

use Games\TicTacToe\Api;
use Games\TicTacToe\GameState;
use Games\TicTacToe\Interfaces\EngineInterface;
use PHPUnit\Framework\TestCase;
use Tests\Traits\Faker;
use Games\TicTacToe\Move;

/**
 * @covers \Games\TicTacToe\Api
 */
final class ApiTest extends TestCase
{
    use Faker;

    /** @var EngineInterface */
    protected $mockEngine;

    /** @var Api */
    protected $stubbedApi;

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
     * @dataProvider badEngineClassProvider
     */
    public function testInitializeWithBadClassFails(string $argument)
    {
        $this->expectException(\InvalidArgumentException::class);
        new Api($argument);
    }

    public function testWithMockEngine()
    {
        $api = new Api(get_class($this->mockEngine));
        $game = $this->createMock(GameState::class);
        $this->assertInstanceOf(Api::class, $api);
        $this->assertInstanceOf(EngineInterface::class, $api->getEngine($game));
    }

    /**
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
     * @dataProvider getWinnerProvider
     */
    public function testGetWinner($boardstate, $expected)
    {
        $this->assertSame($expected, $this->stubbedApi->getWinner($boardstate));
    }



    public function badEngineClassProvider()
    {
        return [
            'Test Class'               => [self::class],
            'Random Class'             => [\DateTime::class],
            'Non-Instantionable Class' => [Engine::class],
        ];
    }

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
