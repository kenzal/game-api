<?php
namespace App\Interfaces;

interface GameStateInterface
{
    public static function getNewGame();

    public function isEndGame();
}
