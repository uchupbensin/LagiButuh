<!-- Tambahkan ini di bagian bawah file -->
<?php 
if ($auth->isLoggedIn()) {
    // Cek apakah user adalah driver atau passenger
    $isParticipant = $user['id'] == $ride['user_id'] || 
        $db->query(
            "SELECT id FROM nebeng_bookings 
            WHERE ride_id = ? AND passenger_id = ?",
            [$ride['id'], $user['id']]
        )->fetch();
    
    if ($isParticipant) {
        $chatController = new ChatController();
        $roomId = $chatController->getOrCreateRoom(
            'nebeng', 
            $ride['id'], 
            [$ride['user_id'], $user['id']]
        );
        
        echo '<script>
        $(document).ready(function() {
            loadChatRoom('.$roomId.');
            $("#open-chat").click();
        });
        </script>';
    }
}

// Include chat box component
require_once __DIR__ . '/../components/chat_box.php';
?>