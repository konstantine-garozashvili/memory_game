const express = require('express');
const http = require('http');
const socketIo = require('socket.io');

const app = express();
const server = http.createServer(app);
const io = socketIo(server);

const PORT = process.env.PORT || 3000;

// Store game states
let games = {};

io.on('connection', (socket) => {
    console.log('New client connected:', socket.id);

    socket.on('joinGame', (gameId, playerId) => {
        socket.join(gameId);
        if (!games[gameId]) {
            games[gameId] = { players: [], board: createShuffledDeck(10), currentPlayer: null };
        }
        games[gameId].players.push(playerId);

        if (games[gameId].players.length === 2) {
            games[gameId].currentPlayer = games[gameId].players[0];
            io.to(gameId).emit('startGame', games[gameId]);
        }
    });

    socket.on('flipCard', (gameId, cardIndex) => {
        const game = games[gameId];
        if (game && game.currentPlayer === socket.id) {
            io.to(gameId).emit('cardFlipped', cardIndex);
            // Add logic to check for matches and switch turns
        }
    });

    socket.on('disconnect', () => {
        console.log('Client disconnected:', socket.id);
        // Handle player disconnection
    });
});

function createShuffledDeck(numPairs) {
    const deck = Array.from({ length: numPairs }, (_, i) => i + 1).flatMap(i => [i, i]);
    return deck.sort(() => Math.random() - 0.5);
}

server.listen(PORT, () => console.log(`Server running on port ${PORT}`));
