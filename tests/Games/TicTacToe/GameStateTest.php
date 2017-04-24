<?php

namespace Tests\Games\TicTacToe;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Move;
use PHPUnit\Framework\TestCase;

/**
 * PHPUnit Test to cover Tic-Tac-Toe GameState class
 *
 * @covers \Games\TicTacToe\GameState
 */
class GameStateTest extends TestCase
{
    const EMPTY_GAME_STRING = '         ';
    const EMPTY_GAME_ARRAY  = [
        [' ', ' ', ' '],
        [' ', ' ', ' '],
        [' ', ' ', ' ']
    ];

    /**
     * Test that GameState can not be instantiated directly
     */
    public function testCannotInstantiateWithNew()
    {
        $this->expectException(\Error::class);
        new GameState;
    }

    /**
     * Verifies the Default configuration of a new game
     */
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

    /**
     * Verifies that a newgame with custom player tokens makes use of them
     */
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
     * Tests that a GameState::newGame() fails with exception when provided with bad arguments
     *
     * @param mixed $arguments ...arguments to pass to GameState::getNewGame()
     *
     * @dataProvider newGameBadArgumentProvider
     */
    public function testNewGameBadArguments(...$arguments)
    {
        $this->expectException(\BadMethodCallException::class);
        GameState::getNewGame(...$arguments);
    }

    /**
     * Tests creating a GameState from a string with default arguments
     */
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

    /**
     * Tests creating a GameState from a string with a custom symbol for turn
     */
    public function testCreateNewGameFromStringWithCustomTurnSymbol()
    {
        $symbol = 'H';
        $game = GameState::createFromString(self::EMPTY_GAME_STRING, $symbol);

        $this->assertSame($symbol, $game->getPlayerASymbol());

        $this->assertSame($game->getPlayerASymbol(), $game->getTurnToMove());
        $this->assertFalse($game->isEndGame());

        $this->assertSame(self::EMPTY_GAME_STRING, $game->asString());
    }

    /**
     * Tests creating a GameState from a string with a custom symbol in the board string
     */
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
     * Tests that exception is thrown when attempting to create a game with bad arguments
     *
     * @param mixed  $boardString      board string argument
     * @param mixed  $symbol           turn symbol argument
     * @param string $exceptionClass   expected exception class
     * @param string $exceptionMessage expected exception message
     *
     * @dataProvider gameFromStringBadArgumentProvider
     */
    public function testGameFromStringWithBadArguments($boardString, $symbol, $exceptionClass, $exceptionMessage)
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);
        GameState::createFromString($boardString, $symbol);
    }

    /**
     * Tests that GameState::createFromArray() functions at the most basic usage
     */
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


    /**
     * Tests that GameState::createFromArray() functions with empty strings instead of spaces
     */
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

    /**
     * Tests that GameState::createFromArray() functions with custom turn symbol
     */
    public function testCreateNewGameFromArrayWithCustomTurnSymbol()
    {
        $symbol = 'H';
        $game = GameState::createFromArray(self::EMPTY_GAME_ARRAY, $symbol);

        $this->assertSame($symbol, $game->getPlayerASymbol());

        $this->assertSame($game->getPlayerASymbol(), $game->getTurnToMove());
        $this->assertFalse($game->isEndGame());

        $this->assertSame(self::EMPTY_GAME_STRING, $game->asString());
    }

    /**
     * Tests that GameState::createFromArray() functions custom symbols in the array
     */
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
     * Tests that GameState::createFromArray() throws exceptions when passed bad arguments
     *
     * @param array $boardArray bad board arrays
     *
     * @dataProvider gameFromArrayBadArgumentProvider
     */
    public function testGameFromArrayWithBadArguments(array $boardArray)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Game State');
        GameState::createFromArray($boardArray);
    }

    /**
     * Tests GameState::isEndGame() method for multiple end-game boards
     *
     * @param GameState $game game state
     *
     * @dataProvider validEndGameProvider
     */
    public function testValidEndGame(GameState $game)
    {
        $this->assertTrue((bool)$game->isEndGame());
    }


    /**
     * Tests that a game states createFrom*() and as*() are interoperable
     *
     * @param string $board game board for testing
     *
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
     * Verifies that GameState::getValidMoves() returns false at all end game states
     *
     * @param GameState $game game state
     *
     * @dataProvider validEndGameProvider
     */
    public function testGetValidMovesReturnsFalseForEndgame(GameState $game)
    {
        $this->assertTrue((bool)$game->isEndGame());
        $this->assertFalse($game->getValidMoves());
    }

    /**
     * Verifies that all possible moves are available for brand new games
     */
    public function testGetValidMovesReturnsAllMovesForNewGame()
    {
        $game = GameState::createFromString(self::EMPTY_GAME_STRING);
        $moves = $game->getValidMoves();
        $this->assertCount(9, $moves);
        $this->assertContainsOnlyInstancesOf(Move::class, $moves);
    }

    /**
     * Verifies that all open positions are returned as valid moves for ongoing games
     *
     * @param string $board game state string
     *
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

    /**
     * Verifies that GameState::makeMove returns a different object for the next game state
     */
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
     * Tests the GameState::getWinner() method
     *
     * @param GameState $board    gameState
     * @param mixed     $expected expected value
     *
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

    /**
     * Provider - winTester Arguments
     *
     * @uses validEndGameStrings()
     * @uses validMidGameStrings()
     *
     * @return array[] Array of arguments arrays - arguments are:
     *                      GameState Game state
     *                      mixed     Expected Result
     */
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

    /**
     * Provider - Bad arguments for new game
     *
     * @return mixed[][]
     */
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

    /**
     * Provider - Bad arguments for GameState::createFromString()
     *
     * @return array[] Array of arguments arrays - arguments are:
     *                      string Game state argument
     *                      mixed  token argument
     *                      string Exception Class Name
     *                      string Exception Message
     */
    public function gameFromStringBadArgumentProvider()
    {
        $newGame       = self::EMPTY_GAME_STRING;
        $symbolMessage = 'Turn must be a player symbol';
        $stateMessage  = 'Invalid Game State';
        return [
            'Empty Marker'    => [$newGame,     '',     \InvalidArgumentException::class, $symbolMessage],
            'Space Marker'    => [$newGame,     ' ',    \InvalidArgumentException::class, $symbolMessage],
            'Null Marker'     => [$newGame,     null,   \InvalidArgumentException::class, $symbolMessage],
            'Long Marker'     => [$newGame,     'LONG', \InvalidArgumentException::class, $symbolMessage],
            'Board too Big'   => ['          ', 'X',    \InvalidArgumentException::class, $stateMessage],
            'Board too Small' => ['        ',   'X',    \InvalidArgumentException::class, $stateMessage],
            'Unknown Symbol'  => ['XO       ',  '+',    \InvalidArgumentException::class, $stateMessage],
            'Unknown Symbol'  => ['XOH      ',  'X',    \InvalidArgumentException::class, $stateMessage],
        ];
    }

    /**
     * Provider - Bad arguments for GameState::createFromArray()
     *
     * @return array[] Array of arguments arrays - arguments are:
     *                      array Game state argument
     */
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

    /**
     * Provider - End Game GameState Objects
     *
     * @uses validEndGameStrings()
     *
     * @return array[] Array of arguments arrays - arguments are:
     *                      GameState Game state object
     */
    public function validEndGameProvider()
    {
        return array_map(function (string $boardString) {
            return [GameState::createFromString($boardString, 'O')];
        }, $this->validEndGameStrings());
    }

    /**
     * Provider - GameState Strings
     *
     * @uses validEndGameStrings()
     * @uses validMidGameStrings()
     *
     * @return array[] Array of arguments arrays - arguments are:
     *                      string Game Board Strings
     */
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

    /**
     * Data Function - valid end game strings
     *
     * @return string[] array of game board strings meeting end-game conditions
     */
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

    /**
     * Data Function - valid mid game strings
     *
     * @return string[] array of game board strings not meeting end-game conditions
     */
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
}
