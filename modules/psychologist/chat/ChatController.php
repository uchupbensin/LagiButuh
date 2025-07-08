<?php
class ChatController {
    private $db;
    private $auth;

    public function __construct() {
        $this->db = new Database();
        $this->auth = new Auth();
    }

    public function getOrCreateRoom($serviceType, $serviceId, $participantIds) {
        // Cek apakah room sudah ada
        $room = $this->db->query(
            "SELECT id FROM chat_rooms WHERE service_type = ? AND service_id = ?",
            [$serviceType, $serviceId]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            // Buat room baru
            $this->db->query(
                "INSERT INTO chat_rooms (service_type, service_id) VALUES (?, ?)",
                [$serviceType, $serviceId]
            );
            $roomId = $this->db->lastInsertId();

            // Tambahkan participants
            foreach ($participantIds as $userId) {
                $this->db->query(
                    "INSERT INTO chat_participants (room_id, user_id) VALUES (?, ?)",
                    [$roomId, $userId]
                );
            }
        } else {
            $roomId = $room['id'];
        }

        return $roomId;
    }

    public function getMessages($roomId, $userId) {
        // Mark messages as read
        $this->db->query(
            "UPDATE chat_messages SET is_read = TRUE 
            WHERE room_id = ? AND sender_id != ? AND is_read = FALSE",
            [$roomId, $userId]
        );

        // Get messages
        return $this->db->query(
            "SELECT cm.*, u.name as sender_name, u.profile_picture
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.id
            WHERE cm.room_id = ?
            ORDER BY cm.created_at ASC",
            [$roomId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sendMessage($roomId, $userId, $message) {
        // Save message to database
        $this->db->query(
            "INSERT INTO chat_messages (room_id, sender_id, message) 
            VALUES (?, ?, ?)",
            [$roomId, $userId, $message]
        );

        // Return message data
        return $this->db->query(
            "SELECT cm.*, u.name as sender_name, u.profile_picture
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.id
            WHERE cm.id = ?",
            [$this->db->lastInsertId()]
        )->fetch(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount($userId) {
        return $this->db->query(
            "SELECT COUNT(*) as unread_count
            FROM chat_messages cm
            JOIN chat_participants cp ON cm.room_id = cp.room_id
            WHERE cp.user_id = ? AND cm.sender_id != ? AND cm.is_read = FALSE",
            [$userId, $userId]
        )->fetch(PDO::FETCH_ASSOC)['unread_count'];
    }
}