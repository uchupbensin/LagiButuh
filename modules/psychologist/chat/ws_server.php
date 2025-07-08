<?php
// File: modules/psychologist/chat/ws_server.php
// Jalankan file ini dari command line: php modules/psychologist/chat/ws_server.php

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../config/env.php';
require __DIR__ . '/ChatServer.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Chat\ChatServer;

// Buat server WebSocket
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    WEBSOCKET_SERVER_PORT,
    WEBSOCKET_SERVER_HOST
);

// Jalankan server
$server->run();
