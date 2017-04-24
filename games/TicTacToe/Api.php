<?php

namespace Games\TicTacToe;

use Games\TicTacToe\Interfaces\EngineInterface;
use Games\TicTacToe\GameState as TicTacToe;
use Games\TicTacToe\Move;
use Games\TicTacToe\Interfaces\MoveInterface;

class Api implements MoveInterface
{
    /** @var string AI Engine Class */
    protected $engineClass;

    public function __construct(string $engineClass)
    {
        $testGame = TicTacToe::getNewGame();
        try {
            $engine = new $engineClass($testGame);
            if (!$engine instanceof EngineInterface) {
                throw new \InvalidArgumentException('$engineClass must implement TicTacToe\Interfaces\EngineInterface');
            }
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException('$engineClass must implement TicTacToe\Interfaces\EngineInterface');
        }

        $this->engineClass = $engineClass;
    }

    public function getEngine(TicTacToe $game): EngineInterface
    {
        return new $this->engineClass($game);
    }

    public function makeMove($boardState, $playerUnit = 'X')
    {
        $game = TicTacToe::createFromArray($boardState, $playerUnit);
        $move = $this->getEngine($game)->getMove();

        return $move ? $move->asArray() : null;
    }

    protected function getFirstPiece(array $arr)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));
        foreach ($iterator as $element) {
            if ($element && $element != TicTacToe::DEFAULT_UNUSED) {
                return $element;
            }
        }
        return TicTacToe::DEFAULT_CROSSES;
    }


    public function getWinner(array $boardState)
    {
        $game = TicTacToe::createFromArray($boardState, $this->getFirstPiece($boardState));
        return $game->getWinner();
    }
}
