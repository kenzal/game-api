<?php
namespace App\Interfaces;

/**
 * Two-Player Game Interface
 *
 * Should be implemented by all games with exactly two players.
 */
interface TwoPlayerGame
{
    const PLAYER_A = 'WhiteRedOne';
    const PLAYER_B = 'BlackBrownTwo';

    /**
     * Returns the representation of the first player
     * @return mixed first player representation
     */
    public function getPlayerASymbol();

    /**
     * Returns the representation of the second player
     * @return mixed second player representation
     */
    public function getPlayerBSymbol();

    /**
     * Returns the player to move
     *
     * @return mixed The result of either getPlayerASymbol() or getPlayerBSymbol()
     */
    public function getTurnToMove();
}
