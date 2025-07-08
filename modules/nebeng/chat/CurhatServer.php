<?php
// File: modules/nebeng/chat/CurhatServer.php
namespace App\NebengChat;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class CurhatServer implements MessageComponentInterface {
    protected $clients;
    // Menyimpan koneksi berdasarkan ID tumpangan (rideId) sebagai "room"
    protected $rideRooms;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->rideRooms = [];
        echo "Server Chat Nebeng (Curhat) telah dimulai...\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);

        // Ekstrak rideId dan userName dari query string
        $queryString = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryString, $queryParams);
        $rideId = $queryParams['rideId'] ?? null;
        $userName = $queryParams['userName'] ?? 'Tamu';

        if ($rideId) {
            $conn->rideId = $rideId;
            $conn->userName = urldecode($userName);

            // Masukkan koneksi ke dalam "room" berdasarkan rideId
            if (!isset($this->rideRooms[$rideId])) {
                $this->rideRooms[$rideId] = new \SplObjectStorage;
            }
            $this->rideRooms[$rideId]->attach($conn);

            echo "Koneksi baru ({$conn->resourceId}) masuk ke ruang tumpangan #{$rideId} sebagai '{$conn->userName}'\n";
        } else {
            echo "Koneksi ditolak: rideId tidak ditemukan.\n";
            $conn->close();
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $rideId = $from->rideId;
        if (!isset($this->rideRooms[$rideId])) {
            return;
        }

        $data = json_decode($msg, true);
        $message = e($data['message'] ?? '', ENT_QUOTES, 'UTF-8');
        
        // Pesan tidak disimpan ke database, langsung broadcast ke semua klien di room yang sama
        $response = [
            'type' => 'message',
            'senderName' => $from->userName,
            'message' => $message,
            'time' => date('H:i')
        ];

        foreach ($this->rideRooms[$rideId] as $client) {
            // Kirim ke semua koneksi di room yang sama, termasuk pengirim
            // UI akan membedakan mana pesan yang dikirim dan diterima
            $response['isSender'] = ($from === $client);
            $client->send(json_encode($response));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $rideId = $conn->rideId ?? null;
        if ($rideId && isset($this->rideRooms[$rideId])) {
            $this->rideRooms[$rideId]->detach($conn);
            echo "Koneksi {$conn->resourceId} keluar dari ruang tumpangan #{$rideId}.\n";
        }
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Terjadi error: {$e->getMessage()}\n";
        $conn->close();
    }
}
