<style>
    /* Chatbot Icon */
    #chatbot-icon {
        position: fixed;
        bottom: 100px;
        right: 30px;
        background: #3498db;
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 24px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        animation: bounce 2s infinite;
        z-index: 9999;
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-10px);
        }

        60% {
            transform: translateY(-5px);
        }
    }

    /* Chatbox Panel */
    #chatbox {
        display: none;
        /* hide initially */
        position: fixed;
        bottom: 90px;
        right: 100px;
        width: 320px;
        height: 400px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        flex-direction: column;
        /* works when toggled via JS */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        z-index: 9998;
        overflow: hidden;
        /* prevent panel from growing */
    }


    /* Chatbox Header */
    #chatbox-header {
        background: #3498db;
        color: white;
        padding: 12px 15px;
        font-weight: 600;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        text-align: center;
        flex-shrink: 0;
    }

    /* Messages Container */
    #chatbox-messages {
        flex: 1 1 auto;
        /* take all remaining space */
        padding: 10px;
        overflow-y: auto;
        /* scroll inside panel */
        background: #f4f7f9;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* Messages */
    .user-msg,
    .bot-msg {
        padding: 8px 12px;
        border-radius: 15px;
        max-width: 80%;
        word-wrap: break-word;
    }

    .user-msg {
        background: #3498db;
        color: white;
        align-self: flex-end;
    }

    .bot-msg {
        background: #ecf0f1;
        color: #2c3e50;
        align-self: flex-start;
    }

    /* Input Container */
    #chatbox-input {
        border: 1px solid #ccc;
        border-radius: 20px;
        padding: 8px 12px;
        width: calc(100% - 90px);
        outline: none;
    }

    #chatbox-send {
        background: #3498db;
        color: white;
        border: none;
        padding: 8px 15px;
        margin-left: 10px;
        border-radius: 20px;
        cursor: pointer;
    }

    #chatbox-send:hover {
        background: #2980b9;
    }

    /* Flex container for input + button */
    #chatbox-input-container {
        flex-shrink: 0;
        /* always visible */
        display: flex;
        align-items: center;
        padding: 10px;
        border-top: 1px solid #ddd;
        background: #fff;
    }

    .keyword-btn {
        background: #3498db;
        color: white;
        border: none;
        padding: 6px 12px;
        margin: 3px;
        border-radius: 15px;
        cursor: pointer;
        font-size: 13px;
    }
    .keyword-btn:hover {
        background: #2980b9;
    }
</style>


<!-- Chatbot Icon -->
<div id="chatbot-icon">
    💬
</div>

<!-- Chatbox -->
<div id="chatbox">
    <div id="chatbox-header">Chat with us!</div>
    <div id="chatbox-messages"></div>
    <div id="chatbox-input-container">
        <input type="text" id="chatbox-input" placeholder="Ask something..." />
        <button id="chatbox-send">Send</button>
    </div>
</div>


<script>
    const chatbotIcon = document.getElementById('chatbot-icon');
    const chatbox = document.getElementById('chatbox');
    const messagesContainer = document.getElementById('chatbox-messages');
    const sendBtn = document.getElementById('chatbox-send');
    const inputBox = document.getElementById('chatbox-input');

    // --- Define keywords & responses ---
    const keywordResponses = {
        "order": "You can place an order by visiting our products page.",
        "price": "Our prices vary depending on the product. Can you tell me which product?",
        "delivery": "We provide fast delivery within 2-3 business days.",
        "contact": "You can contact us at support@yourshop.com.",
        "help": "We are here to assist you 24/7!."
    };

    // --- Show keyword buttons in greeting ---
    function showKeywordButtons() {
        const botDiv = document.createElement('div');
        botDiv.className = 'bot-msg';

        const text = document.createElement('div');
        text.textContent = "Here are some quick options:";
        botDiv.appendChild(text);

        // add buttons
        Object.keys(keywordResponses).forEach(key => {
            const btn = document.createElement('button');
            btn.className = "keyword-btn";
            btn.textContent = key.charAt(0).toUpperCase() + key.slice(1);
            btn.onclick = () => handleUserMessage(key); // simulate user typing keyword
            botDiv.appendChild(btn);
        });

        messagesContainer.appendChild(botDiv);
        scrollChat();
    }

    chatbotIcon.addEventListener('click', () => {
        if (chatbox.style.display === 'flex') {
            chatbox.style.display = 'none';
        } else {
            chatbox.style.display = 'flex';
            if (messagesContainer.children.length === 0) {
                const botDiv = document.createElement('div');
                botDiv.className = 'bot-msg';
                botDiv.textContent = "Hello! How can I assist you today?";
                messagesContainer.appendChild(botDiv);
                scrollChat();
                showKeywordButtons(); // show buttons immediately
            }
        }
    });

    function scrollChat() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // --- Handle user input ---
    function handleUserMessage(msg) {
        // Show user msg
        const userDiv = document.createElement('div');
        userDiv.className = 'user-msg';
        userDiv.textContent = msg;
        messagesContainer.appendChild(userDiv);
        scrollChat();

        // Try keyword matching first
        let reply = null;
        for (let key in keywordResponses) {
            if (msg.toLowerCase().includes(key)) {
                reply = keywordResponses[key];
                break;
            }
        }

        if (reply) {
            // Direct keyword reply
            const botDiv = document.createElement('div');
            botDiv.className = 'bot-msg';
            botDiv.textContent = reply;
            messagesContainer.appendChild(botDiv);
            scrollChat();
        } else {
            // Fallback: ask server (your current PHP logic)
            fetch('chatbot_response.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'message=' + encodeURIComponent(msg)
            })
            .then(r => r.text())
            .then(botReply => {
                const botDiv = document.createElement('div');
                botDiv.className = 'bot-msg';
                botDiv.textContent = botReply;
                messagesContainer.appendChild(botDiv);
                scrollChat();
            });
        }
    }

    // Send button
    sendBtn.addEventListener('click', () => {
        const userMsg = inputBox.value.trim();
        if (!userMsg) return;
        handleUserMessage(userMsg);
        inputBox.value = '';
    });

    // Enter key
    inputBox.addEventListener('keydown', e => {
        if (e.key === 'Enter') sendBtn.click();
    });
</script>