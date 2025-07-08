<?php
require __DIR__ . '/../../../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;

class NotificationServer implements MessageComponentInterface {
    protected $clients;
    protected $userConnections;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->userConnections = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $queryString = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryString, $queryParams);
        
        if (isset($queryParams['user_id'])) {
            $userId = $queryParams['user_id'];
            $this->userConnections[$userId] = $conn;
            echo "New connection for user: {$userId}\n";
            
            // Send pending notifications
            $this->sendPendingNotifications($userId);
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Not used for notifications, but required by interface
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        
        // Remove from user connections
        $userId = array_search($conn, $this->userConnections, true);
        if ($userId !== false) {
            unset($this->userConnections[$userId]);
            echo "Connection closed for user: {$userId}\n";
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    public function sendNotification($userId, $notification) {
        if (isset($this->userConnections[$userId])) {
            $this->userConnections[$userId]->send(json_encode($notification));
            echo "Notification sent to user: {$userId}\n";
            return true;
        }
        return false;
    }

    private function sendPendingNotifications($userId) {
        $db = new Database();
        $conn = $db->getConnection();
        
        $query = "SELECT * FROM notifications 
                 WHERE user_id = $userId AND is_read = 0 
                 ORDER BY created_at DESC 
                 LIMIT 5";
        
        $result = $conn->query($query);
        
        while ($notification = $result->fetch_assoc()) {
            $notificationData = [
                'type' => 'notification',
                'id' => $notification['id'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'related_id' => $notification['related_id'],
                'related_type' => $notification['related_type'],
                'created_at' => $notification['created_at']
            ];
            
            if ($this->sendNotification($userId, $notificationData)) {
                // Mark as sent (not read yet)
                $conn->query("UPDATE notifications SET is_sent = 1 WHERE id = {$notification['id']}");
            }
        }
    }
}

// Run the server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new NotificationServer()
        )
    ),
    8081 // Different port from chat server
);

echo "Notification WebSocket server started on port 8081\n";
$server->run();