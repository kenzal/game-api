<?php

namespace Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Move;
use Games\TicTacToe\Interfaces\EngineInterface;

/**
 * Tic-Tac-Toe AI Engine Abstract
 */
abstract class EngineAbstract implements EngineInterface
{
    /**
     * Current Game State
     *
     * @var GameState
     */
    protected $state;

    /**
     * Get new Engine
     *
     * @param GameState $gameState Current game state
     */
    public function __construct($gameState)
    {
        $this->state = $gameState;
    }

    /**
     * Returns a list of all considered moves
     *
     * @return false|Move[] List of valid Moves
     */
    abstract public function getConsideredMoves();

    /**
     * Returns a single move from those considered
     *
     * @uses getConsideredMoves()
     *
     * @return false|Move Move to make
     */
    public function getMove()
    {
        $moves = $this->getConsideredMoves();
        return $moves ? $moves[array_rand($moves)] : false;
    }
}
