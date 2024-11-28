const express = require('express');
const http = require('http');
const path = require('path');
const socketIo = require('socket.io');
const pty = require('node-pty');

const app = express();
const server = http.createServer(app);
const io = socketIo(server);

const PORT = process.env.PORT || 8000;

// Serve static files from the "" directory
app.use(express.static(path.join(__dirname, '')));

// Handle socket connection
io.on('connection', (socket) => {
    console.log('New client connected');

    // Spawn a bash shell
    const shell = pty.spawn('bash', [], {
        name: 'xterm-color',
        cols: 80,
        rows: 24,
        cwd: process.env.HOME,
        env: process.env
    });
    shell.resize(100, 40);

    // Send terminal data to the client
    shell.on('data', (data) => {
        socket.emit('output', data);
    });

    // Receive input from the client and send it to the shell
    socket.on('input', (data) => {
        shell.write(data);
    });

    // Handle disconnection
    socket.on('disconnect', () => {
        console.log('Client disconnected');
        shell.kill();
    });
});

server.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});
