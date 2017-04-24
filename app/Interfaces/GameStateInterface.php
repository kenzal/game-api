<?php
namespace App\Interfaces;

/**
 * Basic Game State Interface
 */
interface GameStateInterface
{
    /**
     * Gets an instance representing the state of a new game
     *
     * @return self instance representing the gamestate of a new game
     */
    public static function getNewGame();

    /**
     * Checks to see if the game is over
     *
     * Implementiaton depends on individual game
     *
     * @return mixed
     */
    public function isEndGame();
}
