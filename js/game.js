console.log("game.js is loading");

let gameBoard, flippedCards = [], playerMatches = 0, totalPairs = 10;

document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded and parsed");
    gameBoard = document.getElementById('game-board');
    if (gameBoard) {
        console.log("Game board found, initializing game");
        initGame();
    } else {
        console.error("Game board not found");
    }
});

function initGame() {
    const cards = createShuffledDeck(totalPairs);
    renderGameBoard(cards);
}

function createShuffledDeck(numPairs) {
    console.log("Creating shuffled deck");
    const deck = Array.from({ length: numPairs }, (_, i) => i + 1).flatMap(i => [i, i]);
    return deck.sort(() => Math.random() - 0.5);
}

function renderGameBoard(cards) {
    console.log("Rendering game board");
    gameBoard.innerHTML = '';
    cards.forEach((cardValue, index) => {
        const cardElement = document.createElement('div');
        cardElement.className = 'card';
        cardElement.dataset.value = cardValue;
        cardElement.dataset.index = index;
        cardElement.innerHTML = `
            <div class="card-front"></div>
            <div class="card-back">${cardValue}</div>
        `;
        cardElement.addEventListener('click', () => flipCard(cardElement));
        gameBoard.appendChild(cardElement);
    });
    console.log("Game board rendered with", cards.length, "cards");
}

function flipCard(card) {
    console.log("Card clicked", card.dataset.value);
    if (flippedCards.length === 2 || card.classList.contains('flipped')) return;

    card.classList.add('flipped');
    flippedCards.push(card);

    if (flippedCards.length === 2) {
        setTimeout(checkMatch, 1000);
    }
}

function checkMatch() {
    console.log("Checking for match");
    const [card1, card2] = flippedCards;
    const isMatch = card1.dataset.value === card2.dataset.value;

    if (isMatch) {
        console.log("Match found");
        card1.removeEventListener('click', () => flipCard(card1));
        card2.removeEventListener('click', () => flipCard(card2));
        playerMatches++;
        checkWinCondition();
    } else {
        console.log("No match");
        setTimeout(() => {
            card1.classList.remove('flipped');
            card2.classList.remove('flipped');
        }, 1000);
    }

    flippedCards = [];
}

function checkWinCondition() {
    console.log("Checking win condition");
    if (playerMatches === totalPairs) {
        console.log("Player has won");
        sendGameOverToServer();
    }
}

function sendGameOverToServer() {
    console.log("Sending game over to server");
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
        if (data.winner) {
            alert(`Game Over! ${data.winner === playerId ? 'You win!' : 'You lose!'}`);
            window.location.href = 'dashboard.php'; // Redirect to dashboard after game ends
        } else {
            console.error('Error determining winner:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

console.log("game.js finished loading");
