<?php
// File: modules/nebeng/chat/ws_server.php
// Jalankan file ini dari command line: php modules/nebeng/chat/ws_server.php

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../config/env.php';
require __DIR__ . '/CurhatServer.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\NebengChat\CurhatServer;

// Pastikan port ini berbeda dari chat psikolog
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new CurhatServer()
        )
    ),
    NEBENG_WEBSOCKET_PORT,
    WEBSOCKET_SERVER_HOST
);

echo "Server WebSocket untuk Nebeng berjalan di port " . NEBENG_WEBSOCKET_PORT . "\n";
$server->run();
