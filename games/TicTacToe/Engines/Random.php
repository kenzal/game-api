<?php

namespace Games\TicTacToe\Engines;

/**
 * Tic-Tac-Toe Engine with No Focus
 */
class Random extends EngineAbstract
{

    /**
     * Returns a list of all possible moves
     *
     * @return false|Move[] List of valid Moves
     */
    public function getConsideredMoves()
    {
        return $this->state->getValidMoves();
    }
}
