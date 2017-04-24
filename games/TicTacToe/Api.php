<?php

namespace Games\TicTacToe;

use Games\TicTacToe\GameState as TicTacToe;
use Games\TicTacToe\Interfaces\EngineInterface;
use Games\TicTacToe\Interfaces\MoveInterface;
use Games\TicTacToe\Move;

class Api implements MoveInterface
{
    /**
     *  AI Engine Class
     *
     *  @var string
     */
    protected $engineClass;

    /**
     * Constuctor
     *
     * @param string $engineClass name of class implementing EngineInterface
     *
     * @return Api
     */
    public function __construct(string $engineClass)
    {
        $testGame = TicTacToe::getNewGame();
        try {
            $engine = new $engineClass($testGame);
            if (!$engine instanceof EngineInterface) {
                throw new \InvalidArgumentException('$engineClass must implement TicTacToe\Interfaces\EngineInterface');
            }
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException('$engineClass must implement TicTacToe\Interfaces\EngineInterface');
        }

        $this->engineClass = $engineClass;
    }

    /**
     * Returns an instance of the API's engine for the Tic-Tac-Toe game argument
     *
     * @param TicTacToe $game
     *
     * @return EngineInterface
     */
    public function getEngine(TicTacToe $game): EngineInterface
    {
        return new $this->engineClass($game);
    }

    /**
     * Requests a move for the given game and player token from the game engine
     *
     * @param string[][] $boardState two dimensional array of single character strings representing the board
     * @param string     $playerUnit single character token representing the player to move
     *
     * @return array [xPos, yPos, token] coordinates and token of piece to place
     */
    public function makeMove($boardState, $playerUnit = 'X')
    {
        $game = TicTacToe::createFromArray($boardState, $playerUnit);
        $move = $this->getEngine($game)->getMove();

        return $move ? $move->asArray() : null;
    }

    /**
     * Returns the first player token found on the game board array
     *
     * @param string[][] $arr gameboard array
     * @return string single character token representing the first player located in the array
     */
    protected function getFirstPiece(array $arr)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));
        foreach ($iterator as $element) {
            if ($element && $element != TicTacToe::DEFAULT_UNUSED) {
                return $element;
            }
        }
        return TicTacToe::DEFAULT_CROSSES;
    }

    /**
     * Returns the winner from the current game, if any
     *
     * @param string[][] $boardState two dimensional array of single character strings representing the board
     * @return string|false|null single character string of the winning player's token,
     *                           false on draw game,
     *                           null on unfinished game
     */
    public function getWinner(array $boardState)
    {
        $game = TicTacToe::createFromArray($boardState, $this->getFirstPiece($boardState));
        return $game->getWinner();
    }
}
