<?php
namespace Games\TicTacToe;

require_once __DIR__ . '/../../vendor/autoload.php';

$engines = [
    'Random'     => ['class' => Engines\Random::class,     'description' => 'No strategy, just random placement.'],
    'WinChecker' => ['class' => Engines\WinChecker::class, 'description' => 'Selects a win if presented with one.'],
    'Blocker'    => ['class' => Engines\Blocker::class,    'description' => 'Attempts to block opponent from winning.'],
];

$selectedEngine = $_REQUEST['opponent'] ?? null;
if (!array_key_exists($selectedEngine, $engines)) {
    $selectedEngine = array_keys($engines)[0];
}

function sendBadRequest()
{
    http_response_code(400);
    header("HTTP/1.0 400 Bad Request");
    exit(1);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $api    = new Api($engines[$selectedEngine]['class']);
    $action = $_REQUEST['action'] ?? 'makeMove';
    if ($action == 'makeMove') {
        $board  = $_REQUEST['boardState'] ?? null;
        $player = $_REQUEST['playerUnit'] ?? null;
        if (!is_array($board) || !is_string($player) || strlen($player)!==1) {
            sendBadRequest();
        }
        $move = $api->makeMove($board, $player);
        echo json_encode($move);
        return;
    } elseif ($action == 'getWinner') {
        $board  = $_REQUEST['boardState'] ?? null;
        echo json_encode($api->getWinner($board));
        return;
    }
    sendBadRequest();
}

?>
<html>
    <head>
        <style type="text/css">
            .t3board {
                border-collapse: collapse;
                vertical-align: center;
                text-align: center;
                margin-left:auto;
                margin-right:auto;
            }
            .t3board td {
                border: 0;
                border-top: 4px solid;
                border-left: 4px solid;
                width: 5em;
                height: 5em;
                padding: 0;
                cursor: cell;
            }
            .t3board tr:first-of-type td{
                border-top: 0;
            }
            .t3board tr td:first-of-type {
                border-left: 0;
            }

            .t3board button {
                width: 4em;
                height: 4em;
            }

            .engineList .engineName {
                font-weight: bold;
                font-size: larger;
            }

            .engineList .engineDescription:before {
                content: ' — ';
            }

            #gameForm {
                width: 20em;
                text-align: center;
            }

            #opponentBlock {
                margin-bottom: 1em;
                width: 15em;
                margin-left:auto;
                margin-right:auto;
            }

            #EngineFirst {
                width: 100%;
                height: 2em;
                margin-top: 1em;
                margin-left:auto;
                margin-right:auto;
            }

            #Reset {
                width: 100%;
                height: 2em;
                margin-top: 1em;
                margin-left:auto;
                margin-right:auto;
            }

        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src=".\t3.js"></script>
    </head>
    <body>
        <h1>Tic-Tac-Toe</h1>
        <h2>Welcome to Tic-Tac-Toe</h2>
        <p>The game is played by each player placing their mark in turns in the squares of the 3x3 grid below.</p>
        <p>There are currently <?= count($engines) ?> computer opponents to chose from, each with a different strategy</p>
        <ul class="engineList">
            <?php foreach ($engines as $name => $engine) : ?>
                <li>
                    <span class='engineName'><?= $name ?></span>
                    <span class="engineDescription"><?= $engine['description'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <form id="gameForm">
            <fieldset id="opponentBlock">
                <legend>Opponent</legend>
                <select id="opponent">
                    <?php foreach ($engines as $name => $engine) : ?>
                        <option value="<?= $name ?>" <?= ($name === $selectedEngine) ? 'selected="selected"' : '' ?>>
                            <?= $name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </fieldset>
            <table class="t3board">
                <?php $char=97; foreach (range(0, 2) as $yPos) : ?>
                    <tr>
                        <?php foreach (range(0, 2) as $xPos) : ?>
                            <td></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <button id="EngineFirst">Opponent May Go First</button>
            <button id="Reset">Reset Board</button>
        </form>
    </body>
</html>
