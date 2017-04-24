<?php

namespace App\Abstracts;

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

    abstract public static function getNewGame();

    abstract public function isEndGame();
}
