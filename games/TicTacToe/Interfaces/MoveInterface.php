<?php
namespace Games\TicTacToe\Interfaces;

interface MoveInterface extends \App\Interfaces\MoveInterface
{
    /**
 * Makes a move using the $boardState
 * $boardState contains 2 dimensional array of the game field
 * X represents one team, O - the other team, empty string means field is not yet taken.
 * example
 * [['X', 'O', '']
 *  ['X', 'O', 'O']
 *  ['',  '',  '']]
 * Returns an array, containing x and y coordinates for next move, and the unit that now occupies it.
 * Example: [2, 0, 'O'] - upper right corner - O player
 *
 * @param array  $boardState Current board state
 * @param string $playerUnit Player unit representation
 *
 * @return array
 */
 public function makeMove($boardState, $playerUnit = 'X');
}
