<?php
class NotificationService {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function createNotification($userId, $title, $message, $relatedId = null, $relatedType = null) {
        $title = $this->db->escape($title);
        $message = $this->db->escape($message);
        $relatedType = $relatedType ? $this->db->escape($relatedType) : 'NULL';
        $relatedId = $relatedId ? (int)$relatedId : 'NULL';
        
        $query = "INSERT INTO notifications 
                 (user_id, title, message, related_id, related_type, created_at)
                 VALUES
                 ($userId, '$title', '$message', $relatedId, '$relatedType', NOW())";
        
        return $this->db->query($query);
    }
    
    public function markAsRead($notificationId) {
        $query = "UPDATE notifications SET is_read = 1 WHERE id = $notificationId";
        return $this->db->query($query);
    }
    
    public function getUserNotifications($userId, $unreadOnly = false) {
        $condition = $unreadOnly ? "AND is_read = 0" : "";
        $query = "SELECT * FROM notifications 
                 WHERE user_id = $userId $condition
                 ORDER BY created_at DESC 
                 LIMIT 20";
        
        $result = $this->db->query($query);
        $notifications = [];
        
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        
        return $notifications;
    }
}
?>