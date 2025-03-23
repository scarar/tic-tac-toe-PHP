<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['gameState'])) {
    $_SESSION['gameState'] = ['', '', '', '', '', '', '', '', ''];
    $_SESSION['currentPlayer'] = 'X';
    $_SESSION['gameActive'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    switch ($action) {
        case 'move':
            $index = $data['index'] ?? null;
            if ($index !== null && $_SESSION['gameState'][$index] === '' && $_SESSION['gameActive']) {
                $_SESSION['gameState'][$index] = $_SESSION['currentPlayer'];
                checkForWinner();
                if ($_SESSION['gameActive']) {
                    $_SESSION['currentPlayer'] = $_SESSION['currentPlayer'] === 'X' ? 'O' : 'X';
                }
            }
            break;

        case 'reset':
            $_SESSION['gameState'] = ['', '', '', '', '', '', '', '', ''];
            $_SESSION['currentPlayer'] = 'X';
            $_SESSION['gameActive'] = true;
            break;
    }

    echo json_encode([
        'gameState' => $_SESSION['gameState'],
        'currentPlayer' => $_SESSION['currentPlayer'],
        'gameActive' => $_SESSION['gameActive'],
        'status' => getStatusMessage()
    ]);
    exit;
}

function checkForWinner() {
    $winningConditions = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        [0, 4, 8],
        [2, 4, 6]
    ];

    foreach ($winningConditions as $condition) {
        [$a, $b, $c] = $condition;
        if ($_SESSION['gameState'][$a] !== '' &&
            $_SESSION['gameState'][$a] === $_SESSION['gameState'][$b] &&
            $_SESSION['gameState'][$b] === $_SESSION['gameState'][$c]) {
            $_SESSION['gameActive'] = false;
            return;
        }
    }

    if (!in_array('', $_SESSION['gameState'])) {
        $_SESSION['gameActive'] = false;
    }
}

function getStatusMessage() {
    if (!$_SESSION['gameActive']) {
        if (in_array('', $_SESSION['gameState'])) {
            return 'Player ' . $_SESSION['currentPlayer'] . ' wins!';
        } else {
            return 'Draw!';
        }
    }
    return 'It\'s ' . $_SESSION['currentPlayer'] . '\'s turn';
}