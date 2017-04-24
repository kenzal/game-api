<?php

namespace Games\TicTacToe\Engines;

class WinChecker extends EngineAbstract
{
    public function getConsideredMoves()
    {
        $allMoves = $this->state->getValidMoves();

        $movesForWin = array_filter(
            $allMoves,
            function ($move) {
                return $this->state->makeMove($move)->isEndGame();
            });

        return $movesForWin ?: $allMoves;
    }
}
