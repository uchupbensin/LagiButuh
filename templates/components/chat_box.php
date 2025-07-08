<div id="chat-container" class="fixed bottom-4 right-4 w-80 bg-white rounded-lg shadow-lg flex flex-col" style="height: 400px; display: none;">
    <div class="bg-blue-600 text-white p-3 rounded-t-lg flex justify-between items-center">
        <h3 class="font-bold">Chat</h3>
        <button id="close-chat" class="text-white hover:text-gray-200">×</button>
    </div>
    
    <div id="chat-messages" class="flex-1 p-3 overflow-y-auto">
        <!-- Messages will be loaded here -->
    </div>
    
    <div class="p-3 border-t">
        <div class="flex">
            <input type="text" id="chat-message" placeholder="Ketik pesan..." class="flex-1 border rounded-l-lg p-2 focus:outline-none">
            <button id="send-message" class="bg-blue-600 text-white px-4 rounded-r-lg hover:bg-blue-700">Kirim</button>
        </div>
    </div>
</div>

<button id="open-chat" class="fixed bottom-4 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
    </svg>
    <span id="unread-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" style="display: none;"></span>
</button>

<script>
// WebSocket connection
let chatSocket;
let currentRoomId = null;
let currentUserId = <?php echo $auth->isLoggedIn() ? $auth->getUser()['id'] : 'null'; ?>;

function connectWebSocket() {
    if (!currentUserId) return;

    chatSocket = new WebSocket('ws://localhost:8080');

    chatSocket.onopen = function(e) {
        console.log('WebSocket connected');
        // Authenticate
        chatSocket.send(JSON.stringify({
            type: 'auth',
            userId: currentUserId
        }));
        
        // Load unread count
        updateUnreadCount();
    };

    chatSocket.onmessage = function(event) {
        const data = JSON.parse(event.data);
        
        if (data.type === 'message' && data.roomId === currentRoomId) {
            addMessageToChat(data);
        }
    };

    chatSocket.onclose = function(event) {
        if (event.wasClean) {
            console.log(`WebSocket closed cleanly, code=${event.code}, reason=${event.reason}`);
        } else {
            console.log('WebSocket connection died');
            // Attempt to reconnect after 5 seconds
            setTimeout(connectWebSocket, 5000);
        }
    };

    chatSocket.onerror = function(error) {
        console.log(`WebSocket error: ${error.message}`);
    };
}

function loadChatRoom(roomId) {
    currentRoomId = roomId;
    $('#chat-messages').empty();
    
    // Fetch messages from server
    $.get(`/chat/messages?roomId=${roomId}`, function(messages) {
        messages.forEach(message => {
            addMessageToChat({
                userId: message.sender_id,
                message: message.message,
                timestamp: message.created_at,
                senderName: message.sender_name
            });
        });
    });
    
    // Scroll to bottom
    setTimeout(() => {
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    }, 100);
}

function addMessageToChat(data) {
    const isCurrentUser = data.userId == currentUserId;
    const messageClass = isCurrentUser ? 'bg-blue-100 ml-auto' : 'bg-gray-100 mr-auto';
    
    const messageHtml = `
        <div class="mb-3 max-w-xs ${messageClass} rounded-lg p-3">
            <div class="text-xs font-semibold ${isCurrentUser ? 'text-blue-800' : 'text-gray-800'}">
                ${data.senderName || 'Pengguna'}
            </div>
            <div class="mt-1">${data.message}</div>
            <div class="text-xs text-gray-500 mt-1 text-right">
                ${new Date(data.timestamp).toLocaleTimeString()}
            </div>
        </div>
    `;
    
    $('#chat-messages').append(messageHtml);
    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
}

function sendMessage() {
    const message = $('#chat-message').val().trim();
    if (!message || !currentRoomId || !currentUserId) return;
    
    // Send via WebSocket
    if (chatSocket && chatSocket.readyState === WebSocket.OPEN) {
        chatSocket.send(JSON.stringify({
            type: 'message',
            roomId: currentRoomId,
            userId: currentUserId,
            message: message
        }));
    }
    
    // Clear input
    $('#chat-message').val('');
}

function updateUnreadCount() {
    if (!currentUserId) return;
    
    $.get(`/chat/unread-count?userId=${currentUserId}`, function(response) {
        const unreadCount = response.unreadCount || 0;
        const $unreadBadge = $('#unread-count');
        
        if (unreadCount > 0) {
            $unreadBadge.text(unreadCount).show();
        } else {
            $unreadBadge.hide();
        }
    });
}

// Event listeners
$(document).ready(function() {
    // Toggle chat box
    $('#open-chat').click(function() {
        $('#chat-container').toggle();
        updateUnreadCount();
    });
    
    $('#close-chat').click(function() {
        $('#chat-container').hide();
    });
    
    // Send message
    $('#send-message').click(sendMessage);
    $('#chat-message').keypress(function(e) {
        if (e.which == 13) sendMessage();
    });
    
    // Connect WebSocket
    if (currentUserId) {
        connectWebSocket();
        
        // Check for unread messages every 30 seconds
        setInterval(updateUnreadCount, 30000);
    }
});
</script>