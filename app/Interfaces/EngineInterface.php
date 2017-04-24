<?php
namespace App\Interfaces;

interface EngineInterface
{
    public function __construct($gameState);

    public function getConsideredMoves();

    public function getMove();
}
