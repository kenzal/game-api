<?php
namespace App\Interfaces;

/**
 * Game AI Engine Interface
 */
interface EngineInterface
{
    /**
     * Create a new engine instance for the given game
     *
     * @param App\Interface\GameStateInterface $gameState game state for the engine
     */
    public function __construct($gameState);

    /**
     * Return all considered moves for the current gamestate
     *
     * @return null|mixed[]|\Iterable List of considered moves
     */
    public function getConsideredMoves();

    /**
     * Returns a move for the current game
     *
     * @return mixed game move representation
     */
    public function getMove();
}
