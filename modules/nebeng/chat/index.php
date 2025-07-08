<?php
// File: modules/nebeng/chat/index.php (Diperbaiki)

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Muat file-file yang diperlukan
require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../functions/helper_functions.php';
require_once __DIR__ . '/../../../functions/service_functions.php';

$auth = new Auth();
$urlParts = explode('/', $_GET['url']);
$rideId = end($urlParts);

if (!is_numeric($rideId)) {
    redirect(BASE_URL . '/nebeng/find_ride');
}

$service = new Service();
$rideDetails = $service->getNebengRideDetailsById($rideId);

if (!$rideDetails) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Tumpangan tidak ditemukan.'];
    redirect(BASE_URL . '/nebeng/find_ride');
}

// Tentukan nama pengguna untuk ditampilkan di chat
$userName = 'Tamu-' . substr(uniqid(), -4); 
if ($auth->isLoggedIn()) {
    $user = $auth->getUserById($auth->getUserId());
    $userName = $user['full_name'] ?? $user['username'];
}

$pageTitle = "Chat dengan " . e($rideDetails['driver_name'] ?? 'Pengemudi');
// Gunakan layout penuh tanpa header/footer standar untuk fokus pada chat
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        #chat-box::-webkit-scrollbar { width: 6px; }
        #chat-box::-webkit-scrollbar-thumb { background-color: #9ca3af; border-radius: 3px; }
        #chat-box::-webkit-scrollbar-track { background-color: #e5e7eb; }
    </style>
</head>
<body class="bg-gray-200">
    <div class="container mx-auto p-4 flex justify-center items-center h-screen">
        <div class="w-full max-w-lg h-[90vh] flex flex-col bg-white shadow-2xl rounded-2xl">
            <!-- Header Chat -->
            <header class="bg-gray-800 text-white p-4 rounded-t-2xl shadow-lg">
                <h1 class="text-lg font-bold">Chat Tumpangan: <?php echo e(($rideDetails['origin'] ?? '') . ' ke ' . ($rideDetails['destination'] ?? '')); ?></h1>
                <p class="text-sm text-gray-300">Driver: <?php echo e($rideDetails['driver_name'] ?? 'Pengemudi'); ?></p>
                <p class="text-xs text-yellow-300 mt-1">Perhatian: Riwayat chat ini tidak disimpan.</p>
            </header>

            <!-- Kotak Pesan -->
            <main id="chat-box" class="flex-1 p-6 overflow-y-auto space-y-4 bg-gray-100">
                <!-- Pesan akan muncul di sini -->
                 <div class="text-center text-sm text-gray-500 p-2 bg-gray-200 rounded-lg">Selamat datang, <?php echo e($userName ?? 'Anda'); ?>! Mulai percakapan...</div>
            </main>

            <!-- Input Pesan -->
            <footer class="p-4 bg-white border-t rounded-b-2xl">
                <form id="message-form" class="flex items-center space-x-3">
                    <input type="text" id="message-input" placeholder="Ketik pesan..." autocomplete="off" class="flex-1 px-4 py-2 bg-gray-100 border-2 border-transparent rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors">
                    <button type="submit" class="bg-blue-500 text-white p-3 rounded-full hover:bg-blue-600 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
            </footer>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatBox = document.getElementById('chat-box');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');

        const rideId = <?php echo $rideId; ?>;
        // --- PERBAIKAN DI SINI ---
        const userName = "<?php echo urlencode($userName ?? 'Tamu'); ?>";
        const wsPort = <?php echo NEBENG_WEBSOCKET_PORT; ?>;

        const conn = new WebSocket(`ws://<?php echo WEBSOCKET_SERVER_HOST; ?>:${wsPort}?rideId=${rideId}&userName=${userName}`);

        conn.onopen = function(e) { console.log("Koneksi Curhat-Chat berhasil!"); };
        conn.onclose = function(e) { console.log("Koneksi Curhat-Chat terputus."); };

        conn.onmessage = function(e) {
            const data = JSON.parse(e.data);
            if (!data.isSender) {
                appendMessage(data.senderName, data.message, 'received', data.time);
            }
        };

        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (message === '' || conn.readyState !== WebSocket.OPEN) return;
            
            conn.send(JSON.stringify({ message: message }));
            appendMessage(decodeURIComponent(userName.replace(/\+/g, ' ')), message, 'sent', new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
            messageInput.value = '';
            messageInput.focus();
        });

        function appendMessage(sender, message, type, time) {
            const messageWrapper = document.createElement('div');
            const isSent = type === 'sent';
            
            messageWrapper.classList.add('flex', 'items-end', 'gap-2.5', isSent ? 'justify-end' : 'justify-start');
            
            const messageBubble = `
                <div class="flex flex-col w-full max-w-xs leading-1.5 p-3 border-gray-200 rounded-xl ${isSent ? 'bg-blue-500 text-white rounded-br-none' : 'bg-gray-200 rounded-bl-none'}">
                    ${!isSent ? `<p class="text-sm font-semibold text-gray-900 mb-1">${sender}</p>` : ''}
                    <p class="text-sm font-normal">${message}</p>
                    <span class="text-xs text-right mt-1 ${isSent ? 'text-blue-200' : 'text-gray-500'}">${time}</span>
                </div>
            `;
            
            messageWrapper.innerHTML = messageBubble;
            chatBox.appendChild(messageWrapper);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
</body>
</html>
