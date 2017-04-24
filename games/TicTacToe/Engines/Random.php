<?php

namespace Games\TicTacToe\Engines;

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
