<?php

namespace Tests\Games\TicTacToe;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Move;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Games\TicTacToe\GameState
 */
final class GameStateTest extends TestCase
{
    const EMPTY_GAME_STRING = '         ';
    const EMPTY_GAME_ARRAY  = [
        [' ', ' ', ' '],
        [' ', ' ', ' '],
        [' ', ' ', ' ']
    ];

    /**
     * @expectedException \Error
     */
    public function testCannotInstantiateWithNew()
    {
        new GameState;
    }

    public function testNewGameDefaults()
    {
        $game = GameState::getNewGame();

        $this->assertSame(GameState::DEFAULT_CROSSES, $game->getPlayerASymbol());
        $this->assertSame(GameState::DEFAULT_NOUGHTS, $game->getPlayerBSymbol());
        $this->assertSame(GameState::DEFAULT_UNUSED, $game->getEmptySymbol());

        $this->assertSame($game->getPlayerASymbol(), $game->getTurnToMove());
        $this->assertFalse($game->isEndGame());

        $this->assertSame(self::EMPTY_GAME_STRING, $game->asString());
    }

    public function testNewGameCustomSymbols()
    {
        $first  = '1';
        $second = '2';
        $game = GameState::getNewGame($first, $second);

        $this->assertSame($first, $game->getPlayerASymbol());
        $this->assertSame($second, $game->getPlayerBSymbol());
        $this->assertSame(GameState::DEFAULT_UNUSED, $game->getEmptySymbol());

        $this->assertSame($game->getPlayerASymbol(), $game->getTurnToMove());
        $this->assertFalse($game->isEndGame());

        $this->assertSame(self::EMPTY_GAME_STRING, $game->asString());
    }

    /**
     * @dataProvider newGameBadArgumentProvider
     */
    public function testNewGameBadArguments($first, $second)
    {
        $this->expectException(\BadMethodCallException::class);
        GameState::getNewGame($first, $second);
    }


    public function testCreateNewGameFromStringWithDefaults()
    {
        $game = GameState::createFromString(self::EMPTY_GAME_STRING);

        $this->assertSame(GameState::DEFAULT_CROSSES, $game->getPlayerASymbol());
        $this->assertSame(GameState::DEFAULT_NOUGHTS, $game->getPlayerBSymbol());
        $this->assertSame(GameState::DEFAULT_UNUSED, $game->getEmptySymbol());

        $this->assertSame($game->getPlayerASymbol(), $game->getTurnToMove());
        $this->assertFalse($game->isEndGame());

        $this->assertSame(self::EMPTY_GAME_STRING, $game->asString());
    }

    public function testCreateNewGameFromStringWithCustomTurnSymbol()
    {
        $symbol = 'H';
        $game = GameState::createFromString(self::EMPTY_GAME_STRING, $symbol);

        $this->assertSame($symbol, $game->getPlayerASymbol());

        $this->assertSame($game->getPlayerASymbol(), $game->getTurnToMove());
        $this->assertFalse($game->isEndGame());

        $this->assertSame(self::EMPTY_GAME_STRING, $game->asString());
    }

    public function testGameFromStringWithCustomSymbols()
    {
        $symbol = 'H';
        $boardString = 'HA       ';
        $game = GameState::createFromString($boardString, $symbol);

        $this->assertContains($symbol, [$game->getPlayerASymbol(), $game->getPlayerBSymbol()]);

        $this->assertSame($symbol, $game->getTurnToMove());
        $this->assertFalse($game->isEndGame());

        $this->assertSame($boardString, $game->asString());
    }

    /**
     * @dataProvider gameFromStringBadArgumentProvider
     */
    public function testGameFromStringWithBadArguments($boardString, $symbol, $exceptionClass, $exceptionMessage)
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);
        GameState::createFromString($boardString, $symbol);
    }

    public function testNewGameFromArray()
    {
        $game = GameState::createFromArray(self::EMPTY_GAME_ARRAY);

        $this->assertSame(GameState::DEFAULT_CROSSES, $game->getPlayerASymbol());
        $this->assertSame(GameState::DEFAULT_NOUGHTS, $game->getPlayerBSymbol());
        $this->assertSame(GameState::DEFAULT_UNUSED, $game->getEmptySymbol());

        $this->assertSame($game->getPlayerASymbol(), $game->getTurnToMove());
        $this->assertFalse($game->isEndGame());

        $this->assertSame(self::EMPTY_GAME_STRING, $game->asString());
    }



    public function testFromArrayWithNoSpaces()
    {
        $board = [['','',''],['','',''],['','','']];
        $game = GameState::createFromArray($board);

        $this->assertSame(GameState::DEFAULT_CROSSES, $game->getPlayerASymbol());
        $this->assertSame(GameState::DEFAULT_NOUGHTS, $game->getPlayerBSymbol());
        $this->assertSame(GameState::DEFAULT_UNUSED, $game->getEmptySymbol());

        $this->assertSame($game->getPlayerASymbol(), $game->getTurnToMove());
        $this->assertFalse($game->isEndGame());

        $this->assertSame(self::EMPTY_GAME_STRING, $game->asString());
    }


    public function testCreateNewGameFromArrayWithCustomTurnSymbol()
    {
        $symbol = 'H';
        $game = GameState::createFromArray(self::EMPTY_GAME_ARRAY, $symbol);

        $this->assertSame($symbol, $game->getPlayerASymbol());

        $this->assertSame($game->getPlayerASymbol(), $game->getTurnToMove());
        $this->assertFalse($game->isEndGame());

        $this->assertSame(self::EMPTY_GAME_STRING, $game->asString());
    }

    public function testGameFromArrayWithCustomSymbols()
    {
        $symbol = 'H';
        $boardString = 'HA       ';
        $boardArray = self::EMPTY_GAME_ARRAY;
        $boardArray[0][0] = $symbol;
        $boardArray[0][1] = 'A';
        $game = GameState::createFromArray($boardArray, $symbol);

        $this->assertContains($symbol, [$game->getPlayerASymbol(), $game->getPlayerBSymbol()]);

        $this->assertSame($symbol, $game->getTurnToMove());
        $this->assertFalse($game->isEndGame());

        $this->assertSame($boardString, $game->asString());
    }

    /**
     * @dataProvider gameFromArrayBadArgumentProvider
     */
    public function testGameFromArrayWithBadArguments(array $boardArray)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Game State');
        GameState::createFromArray($boardArray);
    }

    /**
     * @dataProvider validEndGameProvider
     */
    public function testValidEndGame(GameState $game)
    {
        $this->assertTrue((bool)$game->isEndGame());
    }


    /**
     * @dataProvider variousGameStateProvider
     */
    public function testStringToClassToArrayToClassToString(string $board)
    {
        $initClass  = GameState::createFromString($board);
        $this->assertSame($board, $initClass->asString());
        $initArray  = $initClass->asArray();
        $arrayClass = GameState::createFromArray($initArray);
        $this->assertSame($board, $arrayClass->asString());
        $this->assertSame($initArray, $arrayClass->asArray());
        $this->assertEquals($initClass, $arrayClass);
        $this->assertSame($board, (string)$arrayClass);
    }

    /**
     * @dataProvider validEndGameProvider
     */
    public function testGetValidMovesReturnsFalseForEndgame(GameState $game)
    {
        $this->assertTrue((bool)$game->isEndGame());
        $this->assertFalse($game->getValidMoves());
    }

    public function testGetValidMovesReturnsAllMovesForNewGame()
    {
        $game = GameState::createFromString(self::EMPTY_GAME_STRING);
        $moves = $game->getValidMoves();
        $this->assertCount(9, $moves);
        $this->assertContainsOnlyInstancesOf(Move::class, $moves);
    }

    /**
     * @dataProvider variousGameStateProvider
     */
    public function testGetValidMovesReturnsOpenMovesForAnyGame(string $board)
    {
        $availableLocations = substr_count($board, ' ');
        $game = GameState::createFromString($board);
        $moves = $game->getValidMoves();
        if ($game->isEndGame()) {
            $this->assertFalse($moves);
            return;
        }
        $this->assertCount($availableLocations, $moves);
        $this->assertContainsOnlyInstancesOf(Move::class, $moves);
    }

    public function testMakeMove()
    {
        $game = GameState::createFromString(self::EMPTY_GAME_STRING);
        $move = new Move($game, 0, 0); //place X in upper Left
        $this->assertContains(
            $move->asArray(),
            array_map(
                function (Move $move) {
                    return $move->asArray();
                },
                $game->getValidMoves()
            )
        );
        $next = $game->makeMove($move);
        $this->assertInstanceOf(GameState::class, $next);
        $this->assertNotSame($next, $game);
        $this->assertNotEquals($next, $game);
        $this->assertNotSame($next->getTurnToMove(), $game->getTurnToMove());
        $this->assertNotSame($next->asString(), $game->asString());
        $this->assertEquals('X        ', $next->asString());
    }

    /**
     * @dataProvider winTesterProvider
     */
    public function testGetWinner(GameState $board, $expected)
    {
        $this->assertSame(
            $expected,
            $board->getWinner(),
            sprintf(
                "Failed asserting that %s yields %s for gamestate:\n%s",
                'GameState::getWinner()',
                $expected ?: (is_null($expected) ? 'null' : 'false'),
                implode("\n", str_split($board->asString(), 3))
            )
        );
    }

    public function winTesterProvider()
    {
        $endGameConditions = $this->validEndGameStrings();
        $draw  = ['draw' => [GameState::createFromString($endGameConditions['draw']), false]];
        unset($endGameConditions['draw']);
        $xGames = array_combine(
            array_map(
                function ($key) {
                    return "X-{$key}";
                },
                array_keys($endGameConditions)
            ),
            $endGameConditions
        );
        $oGames = array_combine(
            array_map(
                function ($key) {
                    return "O-{$key}";
                },
                array_keys($endGameConditions)
            ),
            array_map(
                function ($board) {
                    return str_replace(['1','2'], ['O','X'], str_replace(['X','O'], ['1', '2'], $board));
                },
                $endGameConditions
            )
        );

        $midConditons = $this->validMidGameStrings();
        $midGames = array_combine(
            array_map(
                function ($key) {
                    return "Mid-{$key}";
                },
                array_keys($midConditons)
            ),
            $midConditons
        );
        $inputs = array_merge(
            $draw,
            array_map(
                function ($boardString) {
                    return [GameState::createFromString($boardString), null];
                },
                $midGames
            ),
            array_map(
                function ($boardString) {
                    return [GameState::createFromString($boardString), 'X'];
                },
                $xGames
            ),
            array_map(
                function ($boardString) {
                    return [GameState::createFromString($boardString), 'O'];
                },
                $oGames
            )
        );
        return $inputs;
    }

    public function newGameBadArgumentProvider()
    {
        return [
            'Same Marker'  => ['X', 'X'],
            'Empty Marker' => ['X', ''],
            'Space Marker' => ['X', ' '],
            'Null Marker'  => ['X', null],
            'Long Marker'  => ['X', 'LONG'],
            'Tricky Markers' => ['XY', ''], //Added because previous check totaled unique characters in map
        ];
    }

    public function gameFromStringBadArgumentProvider()
    {
        $newGame = self::EMPTY_GAME_STRING;
        return [
            'Empty Marker'    => [$newGame,     '',     \InvalidArgumentException::class, 'Turn must be a player symbol'],
            'Space Marker'    => [$newGame,     ' ',    \InvalidArgumentException::class, 'Turn must be a player symbol'],
            'Null Marker'     => [$newGame,     null,   \InvalidArgumentException::class, 'Turn must be a player symbol'],
            'Long Marker'     => [$newGame,     'LONG', \InvalidArgumentException::class, 'Turn must be a player symbol'],
            'Board too Big'   => ['          ', 'X',    \InvalidArgumentException::class, 'Invalid Game State'],
            'Board too Small' => ['        ',   'X',    \InvalidArgumentException::class, 'Invalid Game State'],
            'Unknown Symbol'  => ['XO       ',  '+',    \InvalidArgumentException::class, 'Invalid Game State'],
            'Unknown Symbol'  => ['XOH      ',  'X',    \InvalidArgumentException::class, 'Invalid Game State'],
        ];
    }

    public function gameFromArrayBadArgumentProvider()
    {
        return [
            'Empty Array' => [[]],
            'Too Short'   => [[
                                [' ', ' ', ' '],
                                [' ', ' ', ' ']
                             ]],
            'Too Tall'    => [[
                                [' ', ' ', ' '],
                                [' ', ' ', ' '],
                                [' ', ' ', ' '],
                                [' ', ' ', ' ']
                             ]],
            'Too Fat'     => [[
                                [' ', ' ', ' ', ' '],
                                [' ', ' ', ' ', ' '],
                                [' ', ' ', ' ', ' ']
                             ]],
            'Too Thin'    => [[
                                [' ', ' '],
                                [' ', ' '],
                                [' ', ' ']
                             ]],
        ];
    }

    public function validEndGameStrings()
    {
        return [
            'top'   => 'XXXOO    ',
            'hMid'  => 'OO XXX   ',
            'low'   => 'OO    XXX',
            'left'  => 'XO XO X  ',
            'vMid'  => 'OXO X  X ',
            'right' => ' OXO X  X',
            'ul2lr' => 'XO OX   X',
            'ur2ll' => '  XOXOX  ',
            'multi' => 'XXXXOOXOO',
            'cross' => 'XOXOXOXOX',
            'Tyr'   => 'XXXOXOOXO',
            'draw'  => 'XOXXOXOXO',
        ];
    }

    public function validMidGameStrings()
    {
        return [
            '       X ',
            ' O     X ',
            ' O   X X ',
            ' O   X XO',
            ' O X X XO',
            ' O XOX XO',
            'XO XOX XO',
            'XO XOXOXO',
        ];
    }

    public function validEndGameProvider()
    {
        return array_map(function (string $boardString) {
            return [GameState::createFromString($boardString, 'O')];
        }, $this->validEndGameStrings());
    }

    public function variousGameStateProvider()
    {
        $boards = array_merge(
            $this->validEndGameStrings(),
            $this->validMidGameStrings()
        );
        $boards['new'] = self::EMPTY_GAME_STRING;
        return array_map(function (string $boardString) {
            return [$boardString];
        }, $boards);
    }
}
