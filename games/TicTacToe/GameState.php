<?php

namespace Games\TicTacToe;

use App\Abstracts\GameState as GameStateAbstract;
use App\Interfaces\TwoPlayerGame;

class GameState extends GameStateAbstract implements TwoPlayerGame
{
    const DRAW_GAME       = 'Draw/Cat/Stalemate';
    const DEFAULT_CROSSES = 'X';
    const DEFAULT_NOUGHTS = 'O';
    const DEFAULT_UNUSED  = ' ';
    const UNUSED          = 'UnplayedSpace';

    protected $symbolMap = [];

    /** @var array */
    private $board = null;

    /** @var string */
    protected $turn=null;

    public static function getNewGame(
        $firstPlayer = self::DEFAULT_CROSSES,
        $secondPlayer = self::DEFAULT_NOUGHTS
    ) {
        return new self($firstPlayer, $secondPlayer);
    }

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

    public function getPlayerASymbol()
    {
        return $this->getSymbol(self::PLAYER_A);
    }

    public function getPlayerBSymbol()
    {
        return $this->getSymbol(self::PLAYER_B);
    }

    public function getEmptySymbol()
    {
        return $this->getSymbol(self::UNUSED);
    }

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

    public function isEndGame()
    {
        return !is_null($this->getWinnerInternal());
    }

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

    public function asString()
    {
        return array_reduce(
            array_merge($this->board[0], $this->board[1], $this->board[2]),
            function ($carry, $position) {
                return $carry . $this->getSymbol($position);
            }
        );
    }

    public function __toString()
    {
        return $this->asString();
    }

    public function asArray()
    {
        $board = $this->board;
        array_walk_recursive($board, function (&$position) {
            $position = $this->getSymbol($position);
        });
        return $board;
    }

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

    public function makeMove(Move $move)
    {
        $newState = clone $this;
        $newState->board[$move->getY()][$move->getX()] = $this->turn;
        $newState->turn = $this->getOtherPlayer();
        return $newState;
    }

    protected function getOtherPlayer()
    {
        return $this->turn === self::PLAYER_A ? self::PLAYER_B : self::PLAYER_A;
    }

    public function getTurnToMove()
    {
        return $this->getSymbol($this->turn);
    }

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
