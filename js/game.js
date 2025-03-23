$(document).ready(function() {
    let gameState = [];
    let currentPlayer = 'X';
    let gameActive = true;

    function updateGame(data) {
        gameState = data.gameState;
        currentPlayer = data.currentPlayer;
        gameActive = data.gameActive;
        
        $('.cell').each(function(index) {
            const cellValue = gameState[index];
            $(this).text(cellValue);
            $(this).removeClass('x o');
            if (cellValue !== '') {
                $(this).addClass(cellValue.toLowerCase());
            }
        });
        
        $('#status').text(data.status);
    }

    function handleCellClick(event) {
        if (!gameActive) return;
        
        const clickedCell = $(event.target);
        const clickedCellIndex = clickedCell.data('index');
        
        if (gameState[clickedCellIndex] !== '') return;

        $.ajax({
            url: 'game.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                action: 'move',
                index: clickedCellIndex
            }),
            success: updateGame
        });
    }

    function resetGame() {
        $.ajax({
            url: 'game.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                action: 'reset'
            }),
            success: updateGame
        });
    }

    // Initialize game
    resetGame();
    
    $('.cell').on('click', handleCellClick);
    $('#reset-btn').on('click', resetGame);
});