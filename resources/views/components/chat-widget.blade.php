@auth
<div id="chat-widget" class="chat-widget">
    <!-- Chat Toggle Button -->
    <div class="chat-toggle" id="chat-toggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" fill="white"/>
        </svg>
    </div>
    
    <!-- Chat Container -->
    <div class="chat-container" id="chat-container">
        <div class="chat-header">
            <h3>🤖 AI Assistant</h3>
            <button class="chat-close" id="chat-close">×</button>
        </div>
        
        <div class="chat-messages" id="chat-messages">
            <div class="message ai">
                <div class="message-content">
                    Halo! Selamat datang di PustakaDigital. Saya siap membantu Anda menemukan buku yang Anda cari. Ada yang bisa saya bantu?
                </div>
            </div>
        </div>
        
        <div class="chat-typing" id="chat-typing" style="display: none;">
            <div class="typing-dots">AI sedang mengetik...</div>
        </div>
        
        <!-- Feedback Section -->
        <div class="chat-feedback" id="chat-feedback" style="display: none;">
            <div class="feedback-text">Apakah jawaban ini membantu?</div>
            <div class="feedback-buttons">
                <button class="feedback-btn" data-helpful="true">👍 Ya</button>
                <button class="feedback-btn" data-helpful="false">👎 Tidak</button>
            </div>
        </div>
        
        <form class="chat-input-form" id="chat-form">
            <input type="text" id="chat-input" placeholder="Ketik pesan..." autocomplete="off" />
            <button type="submit" id="chat-send">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" fill="currentColor"/>
                </svg>
            </button>
        </form>
    </div>
</div>
@endauth

<style>
.chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}

.chat-toggle {
    width: 60px;
    height: 60px;
    background: black;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    border: none;
}

.chat-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
}

.chat-toggle span {
    font-size: 24px;
    color: white;
}

.chat-container {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #000;
}

.chat-container.active {
    display: flex;
}

.chat-header {
    background: black;
    color: white;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
}

.chat-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.chat-close {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.2s;
}

.chat-close:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background: white;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.message {
    display: flex;
    margin-bottom: 8px;
}

.message.user {
    justify-content: flex-end;
}

.message-content {
    max-width: 75%;
    padding: 12px 16px;
    border-radius: 18px;
    word-wrap: break-word;
    font-size: 14px;
    line-height: 1.4;
    position: relative;
}

.message.user .message-content {
    background: black;
    color: white;
    border-bottom-right-radius: 4px;
}

.message.ai .message-content {
    background: white;
    color: black;
    border: 1px solid #000;
    border-bottom-left-radius: 4px;
}

.chat-typing {
    padding: 12px 16px;
    background: white;
    border-top: 1px solid #000;
}

.typing-dots {
    color: #333;
    font-style: italic;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.typing-dots::after {
    content: '';
    width: 4px;
    height: 4px;
    background: #333;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

@keyframes typing {
    0%, 60%, 100% { 
        opacity: 0.3;
        transform: translateY(0);
    }
    30% { 
        opacity: 1;
        transform: translateY(-4px);
    }
}

.chat-feedback {
    padding: 12px 16px;
    background: white;
    border-top: 1px solid #000;
    text-align: center;
}

.feedback-text {
    font-size: 12px;
    color: #333;
    margin-bottom: 8px;
}

.feedback-buttons {
    display: flex;
    justify-content: center;
    gap: 12px;
}

.feedback-btn {
    padding: 6px 16px;
    border: 1px solid #000;
    background: white;
    color: black;
    border-radius: 16px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
}

.feedback-btn:hover {
    background: black;
    color: white;
}

.feedback-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.chat-input-form {
    display: flex;
    padding: 12px 16px;
    background: white;
    border-top: 1px solid #000;
    gap: 8px;
    align-items: center;
}

#chat-input {
    flex: 1;
    border: 1px solid #000;
    border-radius: 20px;
    padding: 10px 16px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
    background: white;
    color: black;
}

#chat-input:focus {
    border-color: #000;
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
}

#chat-send {
    background: black;
    color: white;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.2s;
}

#chat-send:hover {
    background: #333;
}

#chat-send:disabled {
    background: #ccc;
    cursor: not-allowed;
}

/* Scrollbar styling */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #000;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #333;
}

/* Mobile responsiveness */
@media (max-width: 480px) {
    .chat-container {
        width: calc(100vw - 40px);
        height: 60vh;
        right: -10px;
    }
    
    .chat-toggle {
        width: 56px;
        height: 56px;
    }
    
    .chat-toggle span {
        font-size: 22px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Chat widget loaded');
    
    const chatToggle = document.getElementById('chat-toggle');
    const chatContainer = document.getElementById('chat-container');
    const chatClose = document.getElementById('chat-close');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const chatMessages = document.getElementById('chat-messages');
    const chatTyping = document.getElementById('chat-typing');
    const chatFeedback = document.getElementById('chat-feedback');
    
    // Debug: Check if elements exist
    console.log('Chat elements found:', {
        toggle: !!chatToggle,
        container: !!chatContainer,
        form: !!chatForm,
        input: !!chatInput,
        messages: !!chatMessages
    });
    
    let currentSessionId = null;
    let lastAiMessage = null;
    
    // Reset session on page load to start fresh conversation
    function resetChatSession() {
        console.log('Resetting chat session');
        currentSessionId = null;
        lastAiMessage = null;
        
        // Clear chat messages except the initial greeting
        const messages = chatMessages.querySelectorAll('.message');
        messages.forEach((message, index) => {
            if (index > 0) { // Keep the first message (greeting)
                message.remove();
            }
        });
        
        // Hide feedback
        hideFeedback();
        
        // End previous session if exists
        endChatSession();
    }
    
    // Reset session when page loads
    resetChatSession();
    
    // Toggle chat
    chatToggle.addEventListener('click', function() {
        console.log('Chat toggle clicked');
        chatContainer.classList.add('active');
        chatInput.focus();
    });
    
    chatClose.addEventListener('click', function() {
        console.log('Chat close clicked');
        chatContainer.classList.remove('active');
        // End session when closing chat
        if (currentSessionId) {
            endChatSession();
        }
    });
    
    // Handle form submission
    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (!message) return;
        
        console.log('Sending message:', message);
        
        // Add user message
        addMessage(message, true);
        chatInput.value = '';
        
        // Show typing indicator
        chatTyping.style.display = 'block';
        chatSend.disabled = true;
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('CSRF Token:', csrfToken);
            
            const response = await fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ message: message })
            });
            
            console.log('Response status:', response.status);
            
            const data = await response.json();
            console.log('Response data:', data);
            
            chatTyping.style.display = 'none';
            
            if (data.success && data.response) {
                addMessage(data.response, false);
                lastAiMessage = data.response;
                // Show feedback after AI response
                setTimeout(() => {
                    showFeedback();
                }, 1000);
            } else {
                addMessage('Maaf, terjadi kesalahan dalam memproses pesan Anda.', false);
            }
        } catch (err) {
            console.error('Chat error:', err);
            chatTyping.style.display = 'none';
            addMessage('Maaf, tidak dapat terhubung ke server. Pastikan Ollama berjalan.', false);
        } finally {
            chatSend.disabled = false;
            chatInput.focus();
        }
    });
    
    function addMessage(content, isUser) {
        console.log('Adding message:', { content: content.substring(0, 50) + '...', isUser });
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isUser ? 'user' : 'ai'}`;
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        messageContent.textContent = content;
        
        messageDiv.appendChild(messageContent);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function showFeedback() {
        chatFeedback.style.display = 'block';
    }
    
    function hideFeedback() {
        chatFeedback.style.display = 'none';
    }
    
    // Handle feedback buttons
    chatFeedback.addEventListener('click', function(e) {
        if (e.target.classList.contains('feedback-btn')) {
            const wasHelpful = e.target.dataset.helpful === 'true';
            
            // Visual feedback
            e.target.classList.add(wasHelpful ? 'helpful' : 'not-helpful');
            
            // Send feedback to server
            sendFeedback(wasHelpful);
            
            // Hide feedback after 2 seconds
            setTimeout(() => {
                hideFeedback();
                // Reset button styles
                document.querySelectorAll('.feedback-btn').forEach(btn => {
                    btn.classList.remove('helpful', 'not-helpful');
                });
            }, 2000);
        }
    });
    
    async function sendFeedback(wasHelpful) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            await fetch('/api/chat/feedback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ 
                    session_id: currentSessionId,
                    was_helpful: wasHelpful 
                })
            });
        } catch (err) {
            console.error('Failed to send feedback:', err);
        }
    }
    
    async function endChatSession() {
        if (currentSessionId) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                await fetch('/api/chat/end-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ session_id: currentSessionId })
                });
                currentSessionId = null;
            } catch (err) {
                console.error('Failed to end chat session:', err);
            }
        }
    }
    
    // End session when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (currentSessionId) {
            endChatSession();
        }
    });
    
    console.log('Chat widget initialization complete');
});
</script> 