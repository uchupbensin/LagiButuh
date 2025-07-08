<?php
// File: modules/psychologist/chat/ChatServer.php
namespace App\Chat;

// Impor library dan file yang dibutuhkan
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../config/database.php';
require __DIR__ . '/../../../functions/chat_functions.php';

class ChatServer implements MessageComponentInterface {
    protected $clients;
    private $chatHandler;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->chatHandler = new \Chat(); // Menggunakan kelas Chat dari file functions
        echo "Server Chat Psikolog telah dimulai...\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        // Simpan koneksi baru untuk dikelola
        $this->clients->attach($conn);

        // Ekstrak query string untuk identifikasi koneksi
        $queryString = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryString, $queryParams);
        
        $conn->userId = $queryParams['userId'] ?? null;
        $conn->consultationId = $queryParams['consultationId'] ?? null;

        echo "Koneksi baru! ({$conn->resourceId}) - User: {$conn->userId}, Konsultasi: {$conn->consultationId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        // Validasi data yang diterima
        if (!isset($data['message']) || !isset($data['receiverId']) || empty($from->consultationId)) {
            return;
        }

        // Simpan pesan ke database
        $this->chatHandler->saveMessage(
            $from->consultationId,
            $from->userId,
            $data['receiverId'],
            $data['message']
        );

        // Kirim pesan ke klien yang relevan (di ruang konsultasi yang sama)
        foreach ($this->clients as $client) {
            // Kirim hanya ke penerima yang berada di sesi konsultasi yang sama
            if ($client->consultationId === $from->consultationId && $client->userId == $data['receiverId']) {
                $client->send(json_encode([
                    'type' => 'message',
                    'senderId' => $from->userId,
                    'message' => e($data['message']), // Sanitasi sebelum dikirim
                    'time' => date('H:i')
                ]));
                break; // Hentikan loop setelah pesan terkirim
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Hapus koneksi saat ditutup
        $this->clients->detach($conn);
        echo "Koneksi {$conn->resourceId} telah terputus.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Terjadi error: {$e->getMessage()}\n";
        $conn->close();
    }
}
