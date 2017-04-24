<?php

namespace App\Abstracts;

/**
 * Abstract Game State Class
 */
abstract class GameState
{
    /**
     * Protected constructor to prevent direct creation
     *
     * @codeCoverageIgnore
     */
    protected function __construct()
    {
    }

    /**
     * Gets an instance representing the state of a new game
     *
     * @return self instance representing the gamestate of a new game
     */
    abstract public static function getNewGame();


    /**
     * Checks to see if the game is over
     *
     * Implementiaton depends on individual game
     *
     * @return mixed
     */
    abstract public function isEndGame();
}
