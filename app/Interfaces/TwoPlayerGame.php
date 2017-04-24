<?php
namespace App\Interfaces;

interface TwoPlayerGame
{
    const PLAYER_A = 'WhiteRedOne';
    const PLAYER_B = 'BlackBrownTwo';

    public function getPlayerASymbol();
    public function getPlayerBSymbol();

    public function getTurnToMove();
}
