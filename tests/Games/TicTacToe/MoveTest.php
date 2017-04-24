<?php
namespace Tests\Games\TicTacToe;

use Games\TicTacToe\Move;
use Games\TicTacToe\GameState;
use PHPUnit\Framework\TestCase;

/**
 * PHPUnit Test covering the Move Class
 *
 * @covers \Games\TicTacToe\Move
 */
final class MoveTest extends TestCase
{
    /**
     * Mock GameState Object
     *
     * @var GameState
     */
    protected $mockState;

    public function setUp()
    {
        $this->mockState = $this->createMock(GameState::class);
    }

    /**
     * Verifies that the class can be created using a valid set of coordinates
     *
     * @param int $xPos X-Position (0-based Column)
     * @param int $yPos Y-Position (0-based Row)
     *
     * @dataProvider validCoordinatesProvider
     */
    public function testCanBeCreatedWithValidCoordinates(int $xPos, int $yPos)
    {
        $this->assertInstanceOf(
            Move::class,
            new Move($this->mockState, $xPos, $yPos)
        );
    }

    /**
     * Verifies that the GetX() and GetY() methods function correctly
     *
     * @param int $xPos X-Position (0-based Column)
     * @param int $yPos Y-Position (0-based Row)
     *
     * @dataProvider validCoordinatesProvider
     */
    public function testGetFunctions(int $xPos, int $yPos)
    {
        $move = new Move($this->mockState, $xPos, $yPos);
        $this->assertSame($xPos, $move->getX());
        $this->assertSame($yPos, $move->getY());
    }

    /**
     * Verifies that the asArray() method returns an array with the expected format
     *
     * @param int $xPos X-Position (0-based Column)
     * @param int $yPos Y-Position (0-based Row)
     *
     * @dataProvider validCoordinatesProvider
     */
    public function testAsArray(int $xPos, int $yPos)
    {
        $pieces = ['X', 'O'];
        $this->mockState->method('getTurnToMove')
             ->will(new \PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls($pieces));
        $move = new Move($this->mockState, $xPos, $yPos);

        foreach ($pieces as $piece) {
            $this->assertSame([$xPos, $yPos, $piece], $move->asArray());
        }
    }

    /**
     * Verifies that a Move can not be created with invalid coordinates
     *
     * @param int $xPos X-Position (0-based Column)
     * @param int $yPos Y-Position (0-based Row)
     *
     * @dataProvider invalidCoordinatesProvider
     */
    public function testCannotBeCreatedWithInvalidCoordinates($xPos, $yPos)
    {
        $this->expectException(\OutOfRangeException::class);

        $this->assertInstanceOf(
            Move::class,
            new Move($this->mockState, $xPos, $yPos)
        );
    }

    public function validCoordinatesProvider()
    {
        $coordinates = [];
        foreach (range(0, 2) as $xPos) {
            foreach (range(0, 2) as $yPos) {
                $coordinates[] = [$xPos, $yPos];
            }
        }
        return $coordinates;
    }

    public function invalidCoordinatesProvider()
    {
        return [
            'X Less than 0'   => [-1,  0],
            'Y Less than 0'   => [ 0, -1],
            'X,Y Less than 0' => [-1, -1],
            'X More than 2'   => [ 3,  0],
            'Y More than 2'   => [ 0,  3],
            'X,Y More than 2' => [ 3,  3],
        ];
    }
}
