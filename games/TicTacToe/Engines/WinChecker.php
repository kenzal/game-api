<?php

namespace Games\TicTacToe\Engines;

class WinChecker extends EngineAbstract
{
    /**
     * Returns a list of moves with a focus on winning
     *
     * Filters all moves to ones that would present a winning scenerio.
     * If no such moves are available, all valid moves are considered
     *
     *
     * @return false|Move[] List of valid Moves
     */
    public function getConsideredMoves()
    {
        $allMoves = $this->state->getValidMoves();

        $movesForWin = array_filter(
            $allMoves,
            function ($move) {
                return $this->state->makeMove($move)->isEndGame();
            }
        );
        return $movesForWin ?: $allMoves;
    }
}
