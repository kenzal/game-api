<?php

namespace Games\TicTacToe;
/**
 * Tic-Tac-Toe Move Class
 */
class Move
{

    /**
     * X-Coordinate (0-based column)
     * @var int
     */
    protected $xCoOrd;


    /**
     * Y-Coordinate (0-based row)
     * @var int
     */
    protected $yCoOrd;


    /**
     * Game State
     * @var GameState tic-tac-toe game state
     */
    protected $gameState;

    /**
     * Construct move from game state and 0-based coordinates
     *
     * @throws  \OutOfRangeException on out-of-range coordiantes
     *
     * @param GameState $game      Tic-Tac-Toe game state
     * @param int       $xLocation X-Coordinate (0-based column)
     * @param int       $yLocation Y-Coordinate (0-based row)
     */
    public function __construct(GameState $game, $xLocation, $yLocation)
    {
        if ($xLocation < 0 || $xLocation > 2 || $yLocation < 0 || $yLocation > 2) {
            throw new \OutOfRangeException('Coordinates must be between (0,0) and (2,2).');
        }
        $this->xCoOrd = $xLocation;
        $this->yCoOrd = $yLocation;
        $this->gameState = $game;
    }

    /**
     * Returns an array representation of the move itself, with coordinates and token
     *
     * @return array [int x, int y, string token]
     */
    public function asArray()
    {
        return [
            $this->xCoOrd,
            $this->yCoOrd,
            $this->gameState->getTurnToMove()
        ];
    }

    /**
     * Returns X-Coordinate (0-based col)
     *
     * @return int X-Coordinate (0-based col)
     */
    public function getX()
    {
        return $this->xCoOrd;
    }

    /**
     * Returns Y-Coordinate (0-based row)
     *
     * @return int Y-Coordinate (0-based row)
     */
    public function getY()
    {
        return $this->yCoOrd;
    }
}
