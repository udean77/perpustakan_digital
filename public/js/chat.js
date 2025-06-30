// Chat Widget JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chat-toggle');
    const chatContainer = document.getElementById('chat-container');
    const chatClose = document.getElementById('chat-close');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const chatTyping = document.getElementById('chat-typing');
    const chatFeedback = document.getElementById('chat-feedback');

    let sessionId = null;
    let currentMessageId = null;

    // Toggle chat
    chatToggle.addEventListener('click', function() {
        chatContainer.classList.toggle('active');
        if (chatContainer.classList.contains('active')) {
            chatInput.focus();
        }
    });

    // Close chat
    chatClose.addEventListener('click', function() {
        chatContainer.classList.remove('active');
    });

    // Handle form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (!message) return;

        // Add user message
        addMessage(message, 'user');
        chatInput.value = '';

        // Show typing indicator
        chatTyping.style.display = 'block';
        chatFeedback.style.display = 'none';

        // Send message to API
        sendMessage(message);
    });

    // Add message to chat
    function addMessage(content, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        messageContent.textContent = content;
        
        messageDiv.appendChild(messageContent);
        chatMessages.appendChild(messageDiv);
        
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Send message to API
    async function sendMessage(message) {
        try {
            const response = await fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    session_id: sessionId
                })
            });

            const data = await response.json();

            if (data.success) {
                // Hide typing indicator
                chatTyping.style.display = 'none';

                // Add AI response
                addMessage(data.response, 'ai');

                // Store session ID for future messages
                if (data.session_id) {
                    sessionId = data.session_id;
                }

                // Store current message ID for feedback
                if (data.message_id) {
                    currentMessageId = data.message_id;
                }

                // Show feedback buttons
                if (data.response) {
                    setTimeout(() => {
                        chatFeedback.style.display = 'block';
                    }, 1000);
                }
            } else {
                // Hide typing indicator
                chatTyping.style.display = 'none';
                
                // Show error message
                addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'ai');
            }
        } catch (error) {
            console.error('Error:', error);
            
            // Hide typing indicator
            chatTyping.style.display = 'none';
            
            // Show error message
            addMessage('Maaf, terjadi kesalahan koneksi. Silakan coba lagi.', 'ai');
        }
    }

    // Handle feedback
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('feedback-btn')) {
            const helpful = e.target.getAttribute('data-helpful') === 'true';
            
            // Send feedback to API
            sendFeedback(helpful);
            
            // Hide feedback section
            chatFeedback.style.display = 'none';
            
            // Disable feedback buttons
            const feedbackBtns = document.querySelectorAll('.feedback-btn');
            feedbackBtns.forEach(btn => {
                btn.disabled = true;
                btn.style.opacity = '0.5';
            });
        }
    });

    // Send feedback to API
    async function sendFeedback(helpful) {
        if (!currentMessageId) return;

        try {
            await fetch('/api/chat/feedback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message_id: currentMessageId,
                    helpful: helpful
                })
            });
        } catch (error) {
            console.error('Error sending feedback:', error);
        }
    }

    // End session when user closes chat
    chatClose.addEventListener('click', function() {
        if (sessionId) {
            endSession();
        }
    });

    // End session
    async function endSession() {
        try {
            await fetch('/api/chat/end-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: sessionId
                })
            });
        } catch (error) {
            console.error('Error ending session:', error);
        }
    }

    // Add CSRF token meta tag if not exists
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = window.Laravel.csrfToken;
        document.head.appendChild(meta);
    }
}); 