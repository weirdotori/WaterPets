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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Toggle chatbox when icon is clicked
    $('#chatbot-icon').click(function() {
        let chatbox = $('#chatbox');
        if (chatbox.is(':visible')) {
            chatbox.hide();
        } else {
            chatbox.css('display', 'flex'); // make flex when visible
        }
    });



    function scrollChat() {
        let chat = $('#chatbox-messages');
        chat.scrollTop(chat.prop("scrollHeight"));
    }

    $('#chatbox-send').click(function() {
        let userMsg = $('#chatbox-input').val().trim();
        if (userMsg !== '') {
            $.post('chatbot_response.php', {
                message: userMsg
            }, function(response) {
                $('#chatbox-messages').append('<div class="user-msg">' + userMsg + '</div>');
                $('#chatbox-messages').append('<div class="bot-msg">' + response + '</div>');
                $('#chatbox-input').val('');
                scrollChat();
            });
        }
    });
</script>