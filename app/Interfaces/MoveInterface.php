<?php
namespace App\Interfaces;

interface MoveInterface
{
    /**
 * Makes a move using the $boardState
 * Returns a representation of the next move
 *
 * @param mixed  $boardState Current board state
 * @param mixed $player Player unit representation
 *
 * @return array
 */
 public function makeMove($boardState, $player);
}
