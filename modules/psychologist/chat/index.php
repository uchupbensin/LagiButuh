<?php
// File: modules/psychologist/chat/index.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Muat semua file yang diperlukan
require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../functions/helper_functions.php';
require_once __DIR__ . '/../../../functions/chat_functions.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

// Ambil ID booking dari URL
$urlParts = explode('/', $_GET['url']);
$bookingId = end($urlParts);
$currentUserId = $auth->getUserId();

$chat = new Chat();
$bookingDetails = $chat->getBookingDetailsForChat($bookingId, $currentUserId);

// Jika booking tidak valid untuk user ini, redirect
if (!$bookingDetails) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Sesi konsultasi tidak valid atau tidak ditemukan.'];
    redirect(BASE_URL . '/profile');
}

// Tentukan siapa lawan bicara
$chatPartnerName = ($currentUserId == $bookingDetails['client_id']) 
    ? $bookingDetails['psychologist_name'] 
    : $bookingDetails['client_name'];
$receiverId = ($currentUserId == $bookingDetails['client_id'])
    ? $bookingDetails['psychologist_id']
    : $bookingDetails['client_id'];

// Ambil riwayat pesan
$historyMessages = $chat->getMessagesByConsultationId($bookingId);

$pageTitle = "Sesi Konsultasi dengan " . e($chatPartnerName);
// Untuk halaman chat, kita gunakan layout penuh tanpa header/footer standar
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
        #chat-box::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 3px; }
        #chat-box::-webkit-scrollbar-track { background-color: #f1f5f9; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Main Chat Area -->
        <div class="flex flex-col flex-1">
            <!-- Header Chat -->
            <header class="bg-white p-4 border-b shadow-sm z-10">
                <div class="container mx-auto flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Konsultasi dengan <?php echo e($chatPartnerName); ?></h2>
                        <p id="connection-status" class="text-sm text-gray-500">Menyambungkan...</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/profile" class="text-sm text-indigo-600 hover:underline">Tutup Sesi</a>
                </div>
            </header>

            <!-- Kotak Pesan -->
            <main id="chat-box" class="flex-1 p-6 overflow-y-auto bg-slate-50">
                <div class="space-y-4">
                    <!-- Riwayat pesan dimuat di sini -->
                    <?php foreach ($historyMessages as $msg): ?>
                        <div class="flex <?php echo ($msg['sender_id'] == $currentUserId) ? 'justify-end' : 'justify-start'; ?>">
                            <div class="max-w-md px-4 py-3 rounded-2xl <?php echo ($msg['sender_id'] == $currentUserId) ? 'bg-indigo-600 text-white rounded-br-lg' : 'bg-white text-gray-800 rounded-bl-lg shadow-sm'; ?>">
                                <p class="text-sm"><?php echo e($msg['message_text']); ?></p>
                                <span class="text-xs opacity-75 block text-right mt-1"><?php echo date('H:i', strtotime($msg['sent_at'])); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>

            <!-- Input Pesan -->
            <footer class="p-4 bg-white border-t">
                <form id="message-form" class="flex items-center space-x-4">
                    <input type="text" id="message-input" placeholder="Ketik pesan Anda di sini..." autocomplete="off" class="flex-1 px-4 py-2 bg-gray-100 border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-colors">
                    <button type="submit" class="bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                            <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
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
        const statusElement = document.getElementById('connection-status');

        const currentUserId = <?php echo $currentUserId; ?>;
        const consultationId = <?php echo $bookingId; ?>;
        const receiverId = <?php echo $receiverId; ?>;
        
        // Selalu scroll ke pesan paling bawah saat halaman dimuat
        chatBox.scrollTop = chatBox.scrollHeight;

        // Koneksi ke WebSocket Server
        const conn = new WebSocket(`ws://<?php echo WEBSOCKET_SERVER_HOST; ?>:<?php echo WEBSOCKET_SERVER_PORT; ?>?userId=${currentUserId}&consultationId=${consultationId}`);

        conn.onopen = function(e) {
            console.log("Koneksi berhasil dibuat!");
            statusElement.textContent = '● Online';
            statusElement.classList.remove('text-gray-500');
            statusElement.classList.add('text-green-600');
        };

        conn.onmessage = function(e) {
            const data = JSON.parse(e.data);
            if (data.type === 'message') {
                appendMessage(data.message, 'received', data.time);
            }
        };

        conn.onclose = function(e) {
            console.log("Koneksi terputus.");
            statusElement.textContent = 'Koneksi terputus. Coba muat ulang halaman.';
            statusElement.classList.remove('text-green-600');
            statusElement.classList.add('text-red-600');
        };
        
        conn.onerror = function(e) {
            console.error("WebSocket Error:", e);
            statusElement.textContent = 'Gagal terhubung ke server chat.';
            statusElement.classList.add('text-red-600');
        }

        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (message === '' || conn.readyState !== WebSocket.OPEN) return;

            const dataToSend = {
                message: message,
                receiverId: receiverId
            };

            conn.send(JSON.stringify(dataToSend));
            appendMessage(message, 'sent', new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
            messageInput.value = '';
        });

        function appendMessage(message, type, time) {
            const messageContainer = document.createElement('div');
            messageContainer.classList.add('flex', type === 'sent' ? 'justify-end' : 'justify-start');
            
            const messageBubble = `
                <div class="max-w-md px-4 py-3 rounded-2xl ${type === 'sent' ? 'bg-indigo-600 text-white rounded-br-lg' : 'bg-white text-gray-800 rounded-bl-lg shadow-sm'}">
                    <p class="text-sm">${message}</p>
                    <span class="text-xs opacity-75 block text-right mt-1">${time}</span>
                </div>
            `;
            
            messageContainer.innerHTML = messageBubble;
            chatBox.querySelector('.space-y-4').appendChild(messageContainer);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
</body>
</html>
