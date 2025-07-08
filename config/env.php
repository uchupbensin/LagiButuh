<?php
// File: config/env.php
// Konfigurasi untuk lingkungan lokal XAMPP.

// Konfigurasi Database (Default XAMPP)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Password default XAMPP biasanya kosong
define('DB_NAME', 'lagibutuh_db');

// Konfigurasi URL Aplikasi
define('BASE_URL', 'http://localhost/lagibutuh-website');

// --- TAMBAHKAN BAGIAN INI ---
// Konfigurasi API Pihak Ketiga
define('Maps_API_KEY', 'AIzaSyAfFN8NuvYyNkewBVMsk9ZNIcUWDEqHg2U');
// --------------------------

// Konfigurasi WebSocket (Ratchet)
define('WEBSOCKET_SERVER_HOST', '127.0.0.1'); // Gunakan 127.0.0.1 untuk localhost
define('WEBSOCKET_SERVER_PORT', 8080);      // Port untuk chat psikolog
define('NEBENG_WEBSOCKET_PORT', 8081);      // Port untuk chat nebeng

// --- Konfigurasi Opsional (bisa diisi nanti jika diperlukan) ---

// Konfigurasi Pusher (jika ingin notifikasi push)
define('PUSHER_APP_ID', '');
define('PUSHER_APP_KEY', '');
define('PUSHER_APP_SECRET', '');
define('PUSHER_APP_CLUSTER', 'ap1');

// Konfigurasi Email (PHPMailer) - Gunakan Mailtrap.io untuk testing lokal
define('SMTP_HOST', 'smtp.mailtrap.io');
define('SMTP_USERNAME', ''); // Isi dengan username Mailtrap Anda
define('SMTP_PASSWORD', ''); // Isi dengan password Mailtrap Anda
define('SMTP_PORT', 2525);
define('SMTP_SECURE', 'tls');

?>