<?php

namespace Games\TicTacToe;

class Move
{
    protected $xCoOrd;
    protected $yCoOrd;
    protected $gameState;

    public function __construct(GameState $game,
        $xLocation, $yLocation)
    {
        if ($xLocation < 0 || $xLocation > 2 || $yLocation < 0 || $yLocation > 2) {
            throw new \OutOfRangeException('Coordinates must be between (0,0) and (2,2).');
        }
        $this->xCoOrd = $xLocation;
        $this->yCoOrd = $yLocation;
        $this->gameState = $game;
    }

    public function asArray()
    {
        return [
            $this->xCoOrd,
            $this->yCoOrd,
            $this->gameState->getTurnToMove()
        ];
    }

    public function getY()
    {
        return $this->yCoOrd;
    }

    public function getX()
    {
        return $this->xCoOrd;
    }
}
