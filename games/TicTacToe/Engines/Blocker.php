<?php

namespace Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;

/**
 * Tic-Tac-Toe Engine with Blocking Focus
 */
class Blocker extends EngineAbstract
{

    /**
     * Returns a list of moves with a focus on blocking
     *
     * Filters all moves to ones that would prevent the opponent from winning.
     * If no such moves are available, all valid moves are considered
     *
     * @uses getNextState()
     *
     * @return false|Move[] List of valid Moves
     */
    public function getConsideredMoves()
    {
        $allMoves = $this->state->getValidMoves();

        $nextState = $this->getNextState();

        $movesForBlock = array_filter(
            $allMoves,
            function ($move) use ($nextState) {
                return $nextState->makeMove($move)->isEndGame();
            }
        );

        return $movesForBlock ?: $allMoves;
    }

    /**
     * Returns the a copy of the current game state with turn-to-move reversed
     *
     * @return GameState the gamestate if current move was skipped
     */
    protected function getNextState()
    {
        return GameState::createFromString(
            $this->state->asString(),
            ($this->state->getTurnToMove() === $this->state->getPlayerASymbol())
                ? $this->state->getPlayerBSymbol()
                : $this->state->getPlayerASymbol()
        );
    }
}
