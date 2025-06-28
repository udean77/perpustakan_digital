<div id="chat-widget" class="chat-widget">
    <!-- Chat Toggle Button -->
    <div class="chat-toggle" id="chat-toggle">
        <span>💬</span>
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
                    Halo! Saya adalah asisten AI PustakaDigital. Ada yang bisa saya bantu?
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
            <button type="submit" id="chat-send">Kirim</button>
        </form>
    </div>
</div>

<style>
.chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.chat-toggle {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: transform 0.3s;
}

.chat-toggle:hover {
    transform: scale(1.1);
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
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
}

.chat-container.active {
    display: flex;
}

.chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-header h3 {
    margin: 0;
    font-size: 16px;
}

.chat-close {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f8f9fa;
}

.message {
    margin-bottom: 10px;
    display: flex;
}

.message.user {
    justify-content: flex-end;
}

.message-content {
    max-width: 80%;
    padding: 10px 12px;
    border-radius: 15px;
    word-wrap: break-word;
    font-size: 14px;
}

.message.user .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.message.ai .message-content {
    background: white;
    color: #333;
    border: 1px solid #e0e0e0;
}

.chat-typing {
    padding: 10px 15px;
    background: white;
    border-top: 1px solid #e0e0e0;
}

.typing-dots {
    color: #666;
    font-style: italic;
    font-size: 14px;
}

.typing-dots::after {
    content: '';
    animation: typing 1.5s infinite;
}

@keyframes typing {
    0%, 20% { content: ''; }
    40% { content: '.'; }
    60% { content: '..'; }
    80%, 100% { content: '...'; }
}

.chat-feedback {
    padding: 10px 15px;
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
    text-align: center;
}

.feedback-text {
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
}

.feedback-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.feedback-btn {
    padding: 5px 12px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 15px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
}

.feedback-btn:hover {
    background: #f0f0f0;
}

.feedback-btn.helpful {
    background: #28a745;
    color: white;
    border-color: #28a745;
}

.feedback-btn.not-helpful {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.chat-input-form {
    display: flex;
    padding: 15px;
    background: white;
    border-top: 1px solid #e0e0e0;
}

#chat-input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    outline: none;
    font-size: 14px;
}

#chat-input:focus {
    border-color: #667eea;
}

#chat-send {
    margin-left: 10px;
    padding: 10px 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
}

#chat-send:hover {
    opacity: 0.9;
}

#chat-send:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .chat-container {
        width: 300px;
        height: 400px;
        bottom: 70px;
        right: 10px;
    }
    
    .chat-toggle {
        width: 50px;
        height: 50px;
    }
    
    .chat-toggle span {
        font-size: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chat-toggle');
    const chatContainer = document.getElementById('chat-container');
    const chatClose = document.getElementById('chat-close');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const chatMessages = document.getElementById('chat-messages');
    const chatTyping = document.getElementById('chat-typing');
    const chatFeedback = document.getElementById('chat-feedback');
    
    let currentSessionId = null;
    let lastAiMessage = null;
    
    // Toggle chat
    chatToggle.addEventListener('click', function() {
        chatContainer.classList.add('active');
        chatInput.focus();
    });
    
    chatClose.addEventListener('click', function() {
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
        
        // Add user message
        addMessage(message, true);
        chatInput.value = '';
        
        // Show typing indicator
        chatTyping.style.display = 'block';
        chatSend.disabled = true;
        
        try {
            const response = await fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            });
            
            const data = await response.json();
            chatTyping.style.display = 'none';
            
            if (data.reply) {
                addMessage(data.reply, false);
                lastAiMessage = data.reply;
                // Show feedback after AI response
                setTimeout(() => {
                    showFeedback();
                }, 1000);
            } else {
                addMessage('Maaf, terjadi kesalahan dalam memproses pesan Anda.', false);
            }
        } catch (err) {
            chatTyping.style.display = 'none';
            addMessage('Maaf, tidak dapat terhubung ke server. Pastikan Ollama berjalan.', false);
        } finally {
            chatSend.disabled = false;
            chatInput.focus();
        }
    });
    
    function addMessage(content, isUser) {
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
            await fetch('/api/chat/feedback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                await fetch('/api/chat/end-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
});
</script> 