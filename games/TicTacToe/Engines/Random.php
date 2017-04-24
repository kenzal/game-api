<?php

namespace Games\TicTacToe\Engines;

class Random extends EngineAbstract
{
    public function getConsideredMoves()
    {
        return $this->state->getValidMoves();
    }
}
