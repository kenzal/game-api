<?php

namespace Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;

class Blocker extends EngineAbstract
{
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
