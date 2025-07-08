<?php
// File: pusher_auth.php
// Endpoint untuk otentikasi private channel Pusher.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/env.php';
require_once 'vendor/autoload.php';

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

$pusher = new Pusher\Pusher(
    PUSHER_APP_KEY,
    PUSHER_APP_SECRET,
    PUSHER_APP_ID,
    ['cluster' => PUSHER_APP_CLUSTER]
);

// Otorisasi langganan ke channel privat
// Channel name format: private-user-USER_ID
echo $pusher->authorizeChannel(
    $_POST['channel_name'],
    $_POST['socket_id']
);
?>
