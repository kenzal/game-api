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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    header('Content-Type: text/plain');
    try {
        $req = json_decode(file_get_contents('php://input'));
    } catch (\Throwable $e) {
        $errMsg  = 'Request must be valid JSON-RPC 2.0 Request';
        $errCode = -32700;
        goto sendBadRequest;
    }

    // check for engine override
    $engineOverride = $req->params->opponent ?? $selectedEngine;
    if (array_key_exists($engineOverride, $engines)) {
        $selectedEngine = $engineOverride;
    }

    $api    = new Api($engines[$selectedEngine]['class']);
    $method = $req->method ?? 'makeMove';
    $id     = $req->id ?? null;
    $params = $req->params ?? (object)[];

    if (!is_string($id) && !is_numeric($id)) {
        $id = null;
        $errMsg = 'Bad Id';
        var_dump($req);
        goto sendBadRequest;
    }

    $board  = $params->boardState ?? null;
    if ($method == 'makeMove') {
        $player = $params->playerUnit ?? null;
        if (!is_array($board) || !is_string($player) || strlen($player)!==1) {
            $errMsg = 'Invalid parameters for method';
            $errCode = -32602;
            goto sendBadRequest;
        }
        $move = $api->makeMove($board, $player);
        echo json_encode([
            'jsonrpc' => '2.0',
            'result'  => $move,
            'id'      => $id,
        ]);
        return;
    } elseif ($method == 'getWinner') {
        echo json_encode([
            'jsonrpc' => '2.0',
            'result'  => $api->getWinner($board),
            'id'      => $id,
        ]);
        return;
    }

    $errMsg = 'Unknown Method';
    $errCode = -32601;

    sendBadRequest: {
        http_response_code(400);
        header("HTTP/1.0 400 Bad Request");
        echo json_encode([
            'jsonrpc' => '2.0',
            'id'      => $id,
            'error'   => [
                'code'    => $errCode ?? -32603,
                'message' => $errMsg  ?? 'Bad Request'
            ],
        ]);
        exit(1);
    }
}

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Games &ndash; Tic-Tac-Toe</title>
        <link href="./t3.css" type="text/css" rel="stylesheet" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="./t3.js"></script>
    </head>
    <body>
        <h1>Tic-Tac-Toe</h1>
        <h2>Welcome to Tic-Tac-Toe</h2>
        <p>The game is played by each player placing their mark in turns in the squares of the 3x3 grid below.</p>
        <p>There are currently <?= count($engines) ?> computer opponents to
            chose from, each with a different strategy</p>
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
