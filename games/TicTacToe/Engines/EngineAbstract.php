<?php

namespace Games\TicTacToe\Engines;

use Games\TicTacToe\GameState;
use Games\TicTacToe\Interfaces\EngineInterface;

abstract class EngineAbstract implements EngineInterface
{
    protected $state;

    public function __construct($gameState)
    {
        $this->state = $gameState;
    }

    abstract public function getConsideredMoves();

    public function getMove()
    {
        $moves = $this->getConsideredMoves();
        return $moves ? $moves[array_rand($moves)] : false;
    }
}
