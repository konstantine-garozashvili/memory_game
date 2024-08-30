console.log("Game.js loaded");

let gameBoard, flippedCards = [], playerMatches = 0, opponentMatches = 0, isYourTurn;

document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded");
    gameBoard = document.getElementById('game-board');
    console.log("Game board element:", gameBoard);
    initGame();
});

function initGame() {
    console.log("Initializing game");
    fetch(`api/get_game_state.php?game_id=${gameId}`)
        .then(response => {
            console.log("Raw response:", response);
            return response.json();
        })
        .then(data => {
            console.log("Game state data:", data);
            if (data.error) {
                console.error('Error:', data.error);
                return;
            }
            renderGameBoard(data.cards);
            updateGameInfo(data);
            startGameLoop();
        })
        .catch(error => console.error('Error:', error));
}

function renderGameBoard(cards) {
    console.log("Rendering game board with cards:", cards);
    if (!gameBoard) {
        console.error("Game board element not found");
        return;
    }
    gameBoard.innerHTML = '';
    cards.forEach((card, index) => {
        const cardElement = document.createElement('div');
        cardElement.className = 'card';
        cardElement.dataset.index = index;
        cardElement.innerHTML = `
            <div class="card-inner">
                <div class="card-front"></div>
                <div class="card-back">${card}</div>
            </div>
        `;
        cardElement.addEventListener('click', () => flipCard(cardElement, index));
        gameBoard.appendChild(cardElement);
    });
    console.log("Game board rendered");
}

// ... rest of your game.js file ...


function flipCard(card, index) {
    if (!isYourTurn || flippedCards.length === 2 || card.classList.contains('flipped')) return;

    card.classList.add('flipped');
    flippedCards.push({ element: card, index: index });

    if (flippedCards.length === 2) {
        setTimeout(checkMatch, 1000);
    }
}

function checkMatch() {
    const [card1, card2] = flippedCards;
    const isMatch = card1.element.querySelector('.card-back').textContent === 
                    card2.element.querySelector('.card-back').textContent;

    if (isMatch) {
        playerMatches++;
        document.getElementById('your-matches').textContent = playerMatches;
    } else {
        card1.element.classList.remove('flipped');
        card2.element.classList.remove('flipped');
        isYourTurn = false;
        document.getElementById('is-your-turn').textContent = 'No';
    }

    flippedCards = [];
    sendMoveToServer(card1.index, card2.index, isMatch);
    checkWinCondition();
}

function sendMoveToServer(index1, index2, isMatch) {
    fetch('api/make_move.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            game_id: gameId,
            player_id: playerId,
            index1: index1,
            index2: index2,
            is_match: isMatch
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error:', data.error);
        } else {
            updateGameInfo(data);
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateGameInfo(data) {
    isYourTurn = data.current_turn === playerId;
    document.getElementById('is-your-turn').textContent = isYourTurn ? 'Yes' : 'No';
    document.getElementById('your-matches').textContent = data.your_matches;
    document.getElementById('opponent-matches').textContent = data.opponent_matches;
}

function checkWinCondition() {
    if (playerMatches === totalPairs) {
        sendGameOverToServer();
    }
}

function sendGameOverToServer() {
    fetch('api/game_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'game_over',
            game_id: gameId,
            player_id: playerId
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Game Over! ${data.winner === playerId ? 'You win!' : 'You lose!'}`);
            window.location.href = 'dashboard.php';
        } else {
            console.error('Error determining winner:', data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

function startGameLoop() {
    setInterval(() => {
        fetch(`api/get_game_state.php?game_id=${gameId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                    return;
                }
                updateGameInfo(data);
                if (data.game_over) {
                    alert(data.winner === playerId ? 'You win!' : 'You lose!');
                    window.location.href = 'dashboard.php';
                }
            })
            .catch(error => console.error('Error:', error));
    }, 5000);
}

initGame();
