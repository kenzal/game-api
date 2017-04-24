<?php

namespace Games\TicTacToe;

use App\Abstracts\GameState as GameStateAbstract;
use App\Interfaces\TwoPlayerGame;

/**
 * Immutable Tic-Tac-Toe Game State Class
 */
class GameState extends GameStateAbstract implements TwoPlayerGame
{
    const DRAW_GAME       = 'Draw/Cat/Stalemate';
    const DEFAULT_CROSSES = 'X';
    const DEFAULT_NOUGHTS = 'O';
    const DEFAULT_UNUSED  = ' ';
    const UNUSED          = 'UnplayedSpace';

    /**
     * Map Array of symbols used internally and externally
     *
     * @var string[]
     */
    protected $symbolMap = [];

    /**
     * Game Board Representation
     *
     * @var string[][] string values should be self::PLAYER_A, self::PLAYER_B, self::UNUSED
     */
    private $board = null;

    /**
     * Current Player
     *
     * @var string value hould be self::PLAYER_A or self::PLAYER_B
     */
    protected $turn=null;

    /**
     * Returns a instance of an unstarted Tic-Tac-Toe game state
     *
     * @param  string $firstPlayer  Single Character Player Token (such as 'X')
     * @param  string $secondPlayer Single Character Player Token (such as 'O')
     *
     * @return self new instance of new game
     */
    public static function getNewGame(
        $firstPlayer = self::DEFAULT_CROSSES,
        $secondPlayer = self::DEFAULT_NOUGHTS
    ) {
        return new self($firstPlayer, $secondPlayer);
    }

    /**
     * Get a new Tic-Tac-Toe game state from a string and player token
     *
     * @throws \InvalidArgumentException on invalid arguments
     *
     * @param  string $gameStateString 9-character string representation of the board
     * @param  string $turn            Single Character Player Token (such as 'X')
     *
     * @return self new instance of game state represented by the arguments
     */
    public static function createFromString($gameStateString, $turn = self::DEFAULT_CROSSES)
    {
        if ($turn == self::DEFAULT_UNUSED || strlen($turn)!==1) {
            throw new \InvalidArgumentException('Turn must be a player symbol');
        }
        $symbols = str_replace(self::DEFAULT_UNUSED, '', count_chars($gameStateString.$turn, 3));
        if (strlen($symbols)>2 || strlen($gameStateString)!==9) {
            throw new \InvalidArgumentException('Invalid Game State');
        }
        $defaultSymbols = [self::DEFAULT_CROSSES, self::DEFAULT_NOUGHTS];

        $game = (strlen($symbols)===2)
              ? new self($symbols[0], $symbols[1])
              : new self($symbols, trim(implode('', $defaultSymbols), $symbols)[0]);

        $game->turn = $game->getFromSymbol($turn);

        $board = [];
        foreach (str_split($gameStateString, 3) as $row) {
            $board[] = array_map([$game, 'getFromSymbol'], str_split($row));
        }
        $game->board = $board;
        return $game;
    }


    /**
     * Get a new Tic-Tac-Toe game state from an array and player token
     *
     * @uses  createFromString()
     *
     * @throws \InvalidArgumentException on invalid arguments
     *
     * @param  string[][] $gameStateArray 3x3 two-dimensional array of strings representing the game board
     * @param  string     $turn           Single Character Player Token (such as 'X')
     *
     * @return self new instance of game state represented by the arguments
     */
    public static function createFromArray(array $gameStateArray, string $turn = self::DEFAULT_CROSSES)
    {
        if (count($gameStateArray) !== 3) {
            throw new \InvalidArgumentException('Invalid Game State');
        }
        //Reset Keys to 0-based integers
        $gameStateArray = array_values($gameStateArray);

        $stateString = array_reduce(
            array_merge($gameStateArray[0], $gameStateArray[1], $gameStateArray[2]),
            function ($carry, $position) {
                $position = (string)$position;
                if ($position == '') {
                    $position = ' ';
                }
                return $carry . $position;
            }
        );
        return self::createFromString($stateString, $turn);
    }

    /**
     * Returns the representation of the first player
     *
     * @return string first player representation character
     */
    public function getPlayerASymbol()
    {
        return $this->getSymbol(self::PLAYER_A);
    }

    /**
     * Returns the representation of the second player
     *
     * @return string second player representation character
     */
    public function getPlayerBSymbol()
    {
        return $this->getSymbol(self::PLAYER_B);
    }

    /**
     * Returns the representation of the second player
     *
     * @return string character representation of unplayed space
     */
    public function getEmptySymbol()
    {
        return $this->getSymbol(self::UNUSED);
    }

    /**
     * Returns if the player being checked for has a winning condition
     *
     * @param  string $checkPlayer should be one of self::PLAYER_A or self::PLAYER_B
     *
     * @return bool player has a winning condition (three-in-a-line)
     */
    protected function checkWinFor($checkPlayer)
    {
        //Array of all 8 win conditions
        $winMap = [
            0b111000000,
            0b000111000,
            0b000000111,
            0b100100100,
            0b010010010,
            0b001001001,
            0b100010001,
            0b001010100,
            ];

        //We'll work with the string representation of the board
        $boardString = $this->asString();

        //Create a map to translate board markers to bits
        $binTranslation = array_map(
            function ($marker) use ($checkPlayer) {
                return (int)($marker===$checkPlayer);
            },
            array_keys($this->symbolMap)
        );
        //Create a numeric version of all positions occupied by previous player
        $binBoard = bindec(str_replace($this->symbolMap, $binTranslation, $boardString));

        $winConditionsMet = array_filter(
            $winMap,
            function ($condition) use ($binBoard) {
                return ($condition == ($condition & $binBoard));
            }
        );

        //Return true if any win conditions are met
        return (bool)$winConditionsMet;
    }

    /**
     * Returns true if End-Game conditions have been reached
     *
     * End-Game conditions for Tic-Tac-Toe include a player achieving a win condition or no possible moves remaining
     *
     * @return boolean End-Game Conditions have been reached
     */
    public function isEndGame()
    {
        return !is_null($this->getWinnerInternal());
    }

    /**
     * Returns the winner or draw if there is one, null if the game is unfinished
     *
     * @return string|null End-Game Conditions strings: self::PLAYER_A, self::PLAYER_B, self::DRAW_GAME
     */
    protected function getWinnerInternal()
    {
        if ($this->checkWinFor(self::PLAYER_A)) {
            return self::PLAYER_A;
        }
        if ($this->checkWinFor(self::PLAYER_B)) {
            return self::PLAYER_B;
        }
        if (strpos($this->asString(), $this->getEmptySymbol())===false) {
            return self::DRAW_GAME;
        }
        return null;
    }

    /**
     * Returns false on draw, player symbol on win, and null on unfinished.
     *
     * @return null|false|string
     */
    public function getWinner()
    {
        $winner = $this->getWinnerInternal();
        switch ($winner) {
            case self::PLAYER_A:
            case self::PLAYER_B:
                return $this->getSymbol($winner);
            case self::DRAW_GAME:
                return false;
            default:
                return null;
        }
    }

    /**
     * Returns the game board as a string
     *
     * Single string of 9 characters (first, second, & third rows, no separator)
     *
     * @return string game board string
     */
    public function asString()
    {
        return array_reduce(
            array_merge($this->board[0], $this->board[1], $this->board[2]),
            function ($carry, $position) {
                return $carry . $this->getSymbol($position);
            }
        );
    }

    /**
     * Magic Method for string casting
     *
     * @uses  asString()
     *
     * @return string game board string
     */
    public function __toString()
    {
        return $this->asString();
    }

    /**
     * Returns the game board as a 3x3 two dimensional array of characters
     *
     * @return string[][] Game Board Array
     */
    public function asArray()
    {
        $board = $this->board;
        array_walk_recursive($board, function (&$position) {
            $position = $this->getSymbol($position);
        });
        return $board;
    }

    /**
     * Returns a list of all valid moves for the current player
     *
     * @return false|Move[] list of valid moves, false on endgame
     */
    public function getValidMoves()
    {
        if ($this->isEndGame()) {
            return false;
        }

        $moves = [];
        foreach ($this->board as $y => $row) {
            foreach ($row as $x => $position) {
                if ($position == self::UNUSED) {
                    $moves[] = new Move($this, $x, $y);
                }
            }
        }
        return $moves;
    }

    /**
     * Returns a new game state with the move made and the next player set to play
     *
     * @param  Move   $move [description]
     * @return [type]       [description]
     */
    public function makeMove(Move $move)
    {
        $newState = clone $this;
        $newState->board[$move->getY()][$move->getX()] = $this->turn;
        $newState->turn = $this->getOtherPlayer();
        return $newState;
    }

    /**
     * Returns the player whose turn it is not
     *
     * @return string self::PLAYER_A or self::PLAYER_B
     */
    protected function getOtherPlayer()
    {
        return $this->turn === self::PLAYER_A ? self::PLAYER_B : self::PLAYER_A;
    }

    /**
     * Returns the token of the current player
     *
     * @return string
     */

    public function getTurnToMove()
    {
        return $this->getSymbol($this->turn);
    }

    /**
     * Protected constructor
     *
     * @throws \BadMethodCallException On Argument Conflicts
     *
     * @param string $firstPlayer  Single Character Player Token (such as 'X')
     * @param string $secondPlayer Single Character Player Token (such as 'O')
     */
    protected function __construct(
        $firstPlayer = self::DEFAULT_CROSSES,
        $secondPlayer = self::DEFAULT_NOUGHTS
    ) {
        $this->symbolMap = [
            self::PLAYER_A => $firstPlayer,
            self::PLAYER_B => $secondPlayer,
            self::UNUSED   => self::DEFAULT_UNUSED,
        ];
        if (count(array_unique($this->symbolMap)) !== 3) {
            throw new \BadMethodCallException('All Player Symbols must be unique and not a space');
        }
        if (array_filter($this->symbolMap, function ($symbol) {
            return strlen($symbol) !== 1;
        })) {
            throw new \BadMethodCallException('All Player Symbols must be exactly one character');
        }
        $this->board = [
            [self::UNUSED, self::UNUSED, self::UNUSED],
            [self::UNUSED, self::UNUSED, self::UNUSED],
            [self::UNUSED, self::UNUSED, self::UNUSED]];
        $this->turn = self::PLAYER_A;
    }

    /**
     * Returns the Internal Symbol Representation
     *
     * @uses  $symbolMap for lookup
     *
     * @throws \OutOfBoundsException on symbols not found in the map
     *
     * @param  string $symbol External Symbol
     *
     * @return string Internal Symbol
     */
    protected function getFromSymbol($symbol)
    {
        $map = array_flip($this->symbolMap);
        if (!array_key_exists($symbol, $map)) {
            // This condition should never actually be reached unless GameState is extended
            // @codeCoverageIgnoreStart
            throw new \OutOfBoundsException("Unknown Symbol ('{$symbol}').");
            // @codeCoverageIgnoreEnd
        }
        return $map[$symbol];
    }

    /**
     * Returns the External Symbol Representation
     *
     * @uses  $symbolMap for lookup
     *
     * @throws \OutOfBoundsException on symbols not found in the map
     *
     * @param  string $symbol Internal Symbol
     *
     * @return string External Symbol
     */
    protected function getSymbol($position)
    {
        if (!array_key_exists($position, $this->symbolMap)) {
            // This condition should never actually be reached unless GameState is extended
            // @codeCoverageIgnoreStart
            throw new \OutOfBoundsException("Unknown Symbol for Position ('{$position}').");
            // @codeCoverageIgnoreEnd
        }
        return $this->symbolMap[$position];
    }
}
