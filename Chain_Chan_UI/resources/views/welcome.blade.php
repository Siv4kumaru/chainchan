<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <script>// CSRF Token for AJAX</script>
    <title>AI // ChainChan_AI</title>
    <style>
        /* ... YOUR EXISTING CSS ... */
        :root {
            --bg-terminal: #101010; /* Slightly off-black */
            --text-green-user: #33cc33;  /* User - a bit brighter, but not neon */
            --text-green-ai: #22aa22;    /* AI - slightly dimmer than user */
            --text-green-secondary: #118811; /* For less important elements like borders, timestamps */
            --text-green-placeholder: #116611; /* For placeholders, very dim */
            --border-line: #115511;   /* Dark green for borders/separators */
            --font-terminal: 'VT323', 'Lucida Console', 'Courier New', monospace;
            --logout-hover-bg: #aa2222;
            --logout-hover-text: #101010;

            /* --- NEW COLORS --- */
            --text-red-error: #ff4444; /* Brighter, distinct red for errors */
            --text-red-delete: #cc3333; /* Red for delete buttons */
            --text-orange-indicator: #ff9933; /* Orange/Amber for waiting indicators */
        }

        @import url('https://fonts.googleapis.com/css2?family=VT323&display=swap');

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: var(--font-terminal);
            font-size: 18px;
            line-height: 1.4;
        }
        body {
            background-color: var(--bg-terminal);
            color: var(--text-green-user); /* Default to user green */
            display: flex;
        }

        .chat-container {
            display: flex;
            width: 100%;
            height: 100vh;
            background-color: var(--bg-terminal);
            overflow: hidden;
            border: 2px solid var(--border-line);
            box-shadow: inset 0 0 10px rgba(34, 170, 34, 0.05); /* Very subtle inner glow */
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            min-width: 280px;
            background-color: var(--bg-terminal);
            color: var(--text-green-user);
            padding: 15px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border-line);
        }

        .sidebar h2 {
            font-size: 1.1em;
            text-transform: uppercase;
            color: var(--text-green-user); /* Use brighter green for headers */
            margin-top: 20px; /* Added margin for spacing if buttons are above */
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed var(--border-line);
        }

        .sidebar-button { /* --- GENERIC BUTTON STYLE FOR SIDEBAR --- */
            background-color: transparent;
            color: var(--text-green-user);
            border: 1px solid var(--border-line);
            padding: 10px 12px;
            border-radius: 0;
            cursor: pointer;
            text-align: left;
            font-size: 1em;
            margin-bottom: 10px; /* Spacing between buttons */
            width: 100%;
            transition: background-color 0.1s, color 0.1s;
            font-family: var(--font-terminal); /* Ensure font consistency */
            text-decoration: none; /* For <a> tags acting as buttons */
            display: block; /* Ensure <a> tags take full width */
            box-sizing: border-box;
        }
        .sidebar-button:hover {
            background-color: var(--text-green-user);
            color: var(--bg-terminal);
        }
        .sidebar-button:active {
            background-color: var(--text-green-secondary);
            color: var(--bg-terminal);
        }

        /* --- NEW CHAT BUTTON SPECIFIC MARGIN --- */
        #newChatBtn.sidebar-button { /* Ensure specificity if #newChatBtn had other styles */
            margin-bottom: 15px; /* Original margin for new chat button spacing from next element */
        }


        #historyList {
            list-style: none;
            padding: 0;
            margin: 0;
            overflow-y: auto;
            flex-grow: 1; /* Allows history list to take available space */
        }

        #historyList li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            margin-bottom: 3px;
            cursor: default;
            border-radius: 0;
            font-size: 1em;
            position: relative;
        }
        #historyList li .history-item-text {
            padding: 5px 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: clip; /* Changed from ellipsis to clip for more retro feel */
            flex-grow: 1;
            color: var(--text-green-secondary); /* Dimmer for inactive history items */
            cursor: pointer;
        }

        #historyList li:hover .history-item-text,
        #historyList li.active .history-item-text {
            background-color: var(--text-green-user); /* Use user green for active/hover */
            color: var(--bg-terminal);
        }

        #historyList li .delete-chat-btn {
            background: transparent;
            border: none;
            color: var(--text-red-delete); /* MODIFIED: Default red color */
            cursor: pointer;
            padding: 5px 8px;
            font-size: 1.1em;
            opacity: 0.8; /* MODIFIED: Slightly more visible by default */
            transition: color 0.1s, opacity 0.1s, background-color 0.1s;
            line-height: 1;
        }

        #historyList li:hover .delete-chat-btn {
            opacity: 1;
        }
        #historyList li .delete-chat-btn:hover {
            background-color: var(--text-red-delete); /* MODIFIED: Red background on hover */
            color: var(--bg-terminal);        /* MODIFIED: Terminal background color for text */
        }

        /* --- SIDEBAR FOOTER FOR LOGOUT --- */
        .sidebar-footer {
            margin-top: auto; /* Pushes the footer to the bottom */
            padding-top: 15px; /* Space above the logout button */
            border-top: 1px dashed var(--border-line); /* Separator line */
        }

        .logout-button { /* Specific styling for logout button */
           /* Inherits .sidebar-button styles by default */
        }
        .logout-button:hover { /* Override for logout button hover */
            background-color: var(--logout-hover-bg);
            color: var(--logout-hover-text);
            border-color: var(--logout-hover-bg); /* Match border to new bg */
        }
         .logout-button:active { /* Override for logout button active */
            background-color: #881111; /* Darker red on active */
            color: var(--logout-hover-text);
        }


        /* Chat Area */
        .chat-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-terminal);
            overflow: hidden;
            padding: 5px; /* Adjusted padding */
        }

        .chat-header {
            padding: 16px 20px; /* Original padding */
            font-size: 1.4em;   /* Original font size */
            font-weight: 600; /* Stronger weight */
            text-align: left;
            color: var(--text-green-user); /* Keep your original green */
            background-color: #1a1a1a; /* Dark subtle background */
            border-bottom: 2px solid var(--text-green-user); /* Underline it with the same green */
            flex-shrink: 0;
            margin-bottom: 12px; /* Original margin */
            box-shadow: 0 2px 6px rgba(0, 255, 153, 0.1); /* Soft green glow */
            text-transform: uppercase;
            letter-spacing: 0.6px; /* Original letter spacing */
        }


        .messages {
            flex-grow: 1;
            padding: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 8px; /* Spacing between messages */
        }

        .message {
            max-width: 100%; /* Full width for message content */
            padding: 0; /* Remove outer padding, handle internally */
            border-radius: 0;
            line-height: 1.5;
            word-break: break-all; /* Ensure long words break */
            font-size: 1em;
            display: flex; /* Use flex for sender label alignment */
            align-items: flex-start; /* Align items to the top */
        }
        .message p {
            margin: 0; /* Remove default paragraph margin */
            flex-grow: 1; /* Allow message text to take available space */
        }

        .message.placeholder {
            color: var(--text-green-placeholder);
            text-align: center; /* Centers the <p> text as <p> will grow */
            margin-top: 20px;
            font-size: 1em;
            /* Ensure placeholder does not pick up AI styles */
            border-left: none;
            padding-left: 0;
            width: 100%; /* Make placeholder span full width for centering */
            justify-content: center; /* Centers the <p> element if it's not flex-grow:1 */
        }
        .message.placeholder p {
            flex-grow: 0; /* Allow text to center naturally */
        }

        /* --- Error styling for placeholders --- */
        .message.placeholder.error p {
            color: var(--text-red-error) !important; /* Ensure override */
        }


        .message .sender-label {
            margin-right: 8px;
            white-space: nowrap; /* Prevent sender label from wrapping */
        }

        .message.user .sender-label,
        .message.user p {
            color: var(--text-green-user);
        }

        .message.ai {
            /* AI messages might have a slight indent or border */
            border-left: 3px solid var(--text-green-secondary);
            padding-left: 8px; /* Space after the border */
        }

        .message.ai .sender-label,
        .message.ai p {
            color: var(--text-green-ai);
        }


        .timestamp {
            font-size: 0.8em;
            color: var(--text-green-secondary);
            margin-left: 10px;
            white-space: nowrap;
            opacity: 0.7;
            padding-left: 5px; /* Space before timestamp */
        }

        .input-area {
            display: flex;
            padding: 10px 0px; /* Top/bottom padding only */
            border-top: 1px solid var(--border-line);
            background-color: var(--bg-terminal);
            flex-shrink: 0; /* Prevent shrinking */
            align-items: center; /* Vertically align items */
        }
        .input-area .prompt-label {
            color: var(--text-green-user);
            font-size: 1em;
            margin-right: 8px; /* Space after CMD:> */
            padding-left: 10px; /* Padding before CMD:> */
        }

        #userInput {
            flex-grow: 1; /* Take available space */
            padding: 5px 8px;
            border: none; /* No border */
            border-radius: 0; /* No border radius */
            background-color: transparent; /* Transparent background */
            color: var(--text-green-user);
            font-family: var(--font-terminal);
            font-size: 1em;
            outline: none; /* No focus outline */
            caret-color: var(--text-green-user); /* Green caret */
        }
        #userInput::placeholder {
            color: var(--text-green-placeholder);
            opacity: 0.8;
        }

        #sendBtn {
            padding: 5px 10px;
            background-color: transparent;
            color: var(--text-green-user);
            border: 1px solid var(--border-line);
            border-radius: 0;
            cursor: pointer;
            font-size: 1em;
            margin-left: 10px; /* Space before button */
            margin-right: 10px; /* Space after button */
            transition: background-color 0.1s, color 0.1s;
        }
        #sendBtn:hover {
            background-color: var(--text-green-user);
            color: var(--bg-terminal);
        }
        #sendBtn:active {
            background-color: var(--text-green-secondary);
        }


        /* Typing indicator and scrollbar styles remain the same */
        .typing-indicator { display: inline; margin-left: 5px;}
        .typing-indicator span {
            display: inline-block;
            color: var(--text-orange-indicator); /* MODIFIED: Typing dots are now orange */
            animation: blink 1s infinite;
        }
        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-terminal); }
        ::-webkit-scrollbar-thumb { background: var(--border-line); border: 1px solid var(--text-green-secondary); }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-green-secondary); }
        ::-webkit-scrollbar-corner { background: var(--bg-terminal); }
    </style>
</head>
<body>
    <div class="chat-container">
        <aside class="sidebar">
            <button id="newChatBtn" class="sidebar-button">NEW_CHAT ></button>
            @if(Auth::user()->role === 'admin')
            <a id="adminpanel" href="{{ route('knowledge') }}" class="sidebar-button">Admin Panel ></a>
            @endif
            <h2>LOG_FILES:</h2>
            <ul id="historyList"></ul>
            <div class="sidebar-footer">
                <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <button id="logoutBtn" class="sidebar-button logout-button">LOGOUT_SYSTEM</button>
            </div>
        </aside>

        <main class="chat-area">
            <h1 class="chat-header">CHAINCHAN_AI // {"user":"{{ Auth::user()->name }}","role":"{{ Auth::user()->role }}"}</h1>
            <div id="messageContainer" class="messages">
                <div class="message placeholder" id="initialPlaceholderHtml">
                    <p>SYSTEM_INIT...</p>
                </div>
            </div>
            <div class="input-area">
                <span class="prompt-label">CMD:></span>
                <input type="text" id="userInput" placeholder="Enter command..." autofocus>
                <button id="sendBtn">EXEC</button>
            </div>
        </main>
    </div>

    <script>
        const newChatBtn = document.getElementById('newChatBtn');
        const adminPdfBtn = document.getElementById('adminPdfBtn');
        const logoutBtn = document.getElementById('logoutBtn');
        const historyList = document.getElementById('historyList');
        const messageContainer = document.getElementById('messageContainer');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let conversations = [];
        let currentConversation = null;
        const deleteIconChar = "[DEL]";

        // --- CONFIGURATION FOR PYTHON API ---
        // Make sure this URL is correct for your running FastAPI server
        const PYTHON_API_BASE_URL = @json(e($python_api)); // Default FastAPI port is 8000, often dev server on 5001 or similar

        // --- API Helper for Laravel API Calls ---
        async function apiCall(url, method = 'GET', body = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            };
            if (body) {
                options.body = JSON.stringify(body);
            }
            try {
                const response = await fetch(url, options);
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: response.statusText }));
                    console.error(`Laravel API Error ${response.status}: ${errorData.message || 'Unknown error'}`, errorData);
                    throw new Error(`Laravel API Error ${response.status}: ${errorData.message || 'Unknown error'}`);
                }
                if (response.status === 204) return null;
                return response.json();
            } catch (error) {
                console.error('Laravel API call failed:', error);
                throw error;
            }
        }

        // Removed getDummyAIResponse function as it's no longer needed

        function generateClientId() {
            return `client_${Date.now().toString(16).toUpperCase()}_${Math.random().toString(16).slice(2)}`;
        }

        function parseServerConversations(serverData) {
            return serverData.map(conv => ({
                id: conv.id,
                server_id: conv.server_id,
                title: conv.title,
                messages: conv.messages.map(msg => ({
                    sender: msg.sender,
                    text: msg.text,
                    timestamp: msg.timestamp ? new Date(msg.timestamp) : new Date()
                })),
                isUnsaved: false
            }));
        }

        async function loadConversationsFromServer() {
            try {
                const serverData = await apiCall('/web/chat/conversations');
                conversations = parseServerConversations(serverData);
            } catch (error) {
                console.error("Failed to load conversations:", error);
                displayPlaceholder("ERR: FAILED TO LOAD LOG_FILES. CHECK_CONSOLE.", true);
                conversations = [];
            }
        }

        function renderHistory() {
            historyList.innerHTML = '';
            if (conversations.length === 0) {
                const noHistoryItem = document.createElement('li');
                noHistoryItem.innerHTML = `<span class="history-item-text" style="justify-content: center; text-align: center; width: 100%; color: var(--text-green-placeholder);">NO_LOGS</span>`;
                historyList.appendChild(noHistoryItem);
                return;
            }

            const sortedConversations = [...conversations].sort((a, b) => {
                const lastMsgTimestampA = a.messages.length > 0 ? new Date(a.messages[a.messages.length - 1].timestamp).getTime() : 0;
                const lastMsgTimestampB = b.messages.length > 0 ? new Date(b.messages[b.messages.length - 1].timestamp).getTime() : 0;
                return lastMsgTimestampB - lastMsgTimestampA; // Sort by most recent message
            });

            sortedConversations.forEach(conv => {
                const listItem = document.createElement('li');
                listItem.dataset.id = conv.id;
                if (currentConversation && conv.id === currentConversation.id) {
                    listItem.classList.add('active');
                }
                const textSpan = document.createElement('span');
                textSpan.classList.add('history-item-text');
                textSpan.textContent = conv.title || `LOG_${conv.id.slice(-8)}`;
                listItem.appendChild(textSpan);

                const deleteBtn = document.createElement('button');
                deleteBtn.classList.add('delete-chat-btn');
                deleteBtn.textContent = deleteIconChar;
                deleteBtn.title = "DELETE_LOG";

                textSpan.addEventListener('click', () => loadChat(conv.id));
                deleteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    handleDeleteChat(conv.id);
                });
                listItem.appendChild(deleteBtn);
                historyList.appendChild(listItem);
            });
        }

        function formatTimestamp(dateObj) {
            if (!dateObj || !(dateObj instanceof Date) || isNaN(dateObj)) return '[??:??:??]';
            return `[${dateObj.getHours().toString().padStart(2, '0')}:${dateObj.getMinutes().toString().padStart(2, '0')}:${dateObj.getSeconds().toString().padStart(2, '0')}]`;
        }

        function displayMessage(sender, text, timestamp) {
            const initialHtmlPlaceholder = document.getElementById('initialPlaceholderHtml');
            if (initialHtmlPlaceholder) initialHtmlPlaceholder.remove();
            const existingPlaceholder = messageContainer.querySelector('.message.placeholder');
            if (existingPlaceholder) existingPlaceholder.remove();

            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', sender);

            const senderLabel = document.createElement('span');
            senderLabel.classList.add('sender-label');
            senderLabel.textContent = sender === 'user' ? 'USR:> ' : 'SYS:> ';
            messageDiv.appendChild(senderLabel);

            const messageContent = document.createElement('p');
            messageContent.textContent = text; // Text content is safer against XSS
            messageDiv.appendChild(messageContent);

            if (timestamp) {
                const timeElement = document.createElement('span');
                timeElement.classList.add('timestamp');
                timeElement.textContent = formatTimestamp(timestamp);
                messageDiv.appendChild(timeElement);
            }
            messageContainer.appendChild(messageDiv);
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }

        function renderCurrentChat() {
            messageContainer.innerHTML = '';
            if (!currentConversation) {
                displayPlaceholder("ERR: NO_SESSION_ACTIVE. RE-INITIATE.", true);
                disableInput();
                return;
            }
            if (currentConversation.messages.length === 0) {
                displayPlaceholder("AWAITING_CMD...");
            } else {
                currentConversation.messages.forEach(msg => {
                    displayMessage(msg.sender, msg.text, msg.timestamp ? new Date(msg.timestamp) : new Date());
                });
            }
            enableInput();
            userInput.focus();
            renderHistory();
        }

        function displayPlaceholder(text, isError = false) {
            messageContainer.innerHTML = '';
            const placeholderDiv = document.createElement('div');
            placeholderDiv.classList.add('message', 'placeholder');
            if (isError) placeholderDiv.classList.add('error');
            const pElement = document.createElement('p');
            pElement.textContent = text;
            placeholderDiv.appendChild(pElement);
            messageContainer.appendChild(placeholderDiv);
        }

        function enableInput() { userInput.disabled = false; sendBtn.disabled = false; }
        function disableInput() { userInput.disabled = true; sendBtn.disabled = true; }

        function loadChat(clientConversationId) {
            const conversationToLoad = conversations.find(conv => conv.id === clientConversationId);
            if (conversationToLoad) {
                currentConversation = JSON.parse(JSON.stringify(conversationToLoad));
                currentConversation.messages.forEach(msg => {
                    if (msg.timestamp) msg.timestamp = new Date(msg.timestamp);
                });
                renderCurrentChat();
            } else {
                console.warn(`Chat with client ID ${clientConversationId} not found. Starting new.`);
                startNewUnsavedChat();
            }
        }

        function startNewUnsavedChat() {
            const newClientId = generateClientId();
            currentConversation = {
                id: newClientId,
                server_id: null,
                title: `SESS_${new Date().getHours()}${new Date().getMinutes()}${new Date().getSeconds()}`,
                messages: [],
                isUnsaved: true
            };
            renderCurrentChat();
        }

        async function ensureConversationExistsOnServer(conv) {
            if (conv.isUnsaved || !conv.server_id) {
                try {
                    const newServerConv = await apiCall('/web/chat/conversations', 'POST', {
                        client_conversation_id: conv.id,
                        title: conv.title
                    });
                    conv.server_id = newServerConv.server_id;
                    conv.isUnsaved = false;

                    const existingIndex = conversations.findIndex(c => c.id === conv.id);
                    if (existingIndex === -1) {
                        conversations.unshift(JSON.parse(JSON.stringify(conv)));
                    } else {
                        conversations[existingIndex] = JSON.parse(JSON.stringify(conv));
                    }
                    renderHistory(); // Update history list after new conversation is saved
                    return true;
                } catch (error) {
                    console.error("Failed to create conversation on server:", error);
                    displayPlaceholder("ERR: COULD NOT SAVE SESSION. TRY AGAIN.", true);
                    return false;
                }
            }
            return true;
        }

        async function updateConversationTitleOnServer(conv, newTitle) {
            if (!conv.server_id) {
                console.warn("Cannot update title for unsaved conversation on server.");
                return;
            }
            try {
                await apiCall(`/web/chat/conversations/${conv.id}/title`, 'PUT', { title: newTitle });
                conv.title = newTitle;
                const convInList = conversations.find(c => c.id === conv.id);
                if (convInList) convInList.title = newTitle;
                renderHistory();
            } catch (error) {
                console.error("Failed to update conversation title on server:", error);
            }
        }

        async function handleSendMessage() {
            const text = userInput.value.trim();
            if (text === '' || !currentConversation || userInput.disabled) return;

            userInput.disabled = true;
            sendBtn.disabled = true;

            const userMessageTimestamp = new Date();

            // 1. Ensure conversation exists on Laravel server
            const canProceed = await ensureConversationExistsOnServer(currentConversation);
            if (!canProceed) {
                enableInput();
                return;
            }

            // 2. Update title on Laravel server if it's a new chat's first message
            if (currentConversation.messages.length === 0 && currentConversation.title.startsWith('SESS_')) {
                const newTitle = `LOG_${text.substring(0, 15).trim().replace(/\s+/g, '_')}`;
                await updateConversationTitleOnServer(currentConversation, newTitle);
                // currentConversation.title is updated by the function if successful
            }
            // 3. Add user message locally and display
            const userMessage = { sender: 'user', text: text, timestamp: userMessageTimestamp };
            currentConversation.messages.push(userMessage);
            displayMessage('user', text, userMessageTimestamp);

            // 4. Save user message to Laravel server
            try {
                await apiCall(`/web/chat/conversations/${currentConversation.id}/messages`, 'POST', {
                    ...userMessage,
                    timestamp: userMessageTimestamp.toISOString()
                });
                // Update the conversation in the main list for correct sorting/display
                const convIndex = conversations.findIndex(c => c.id === currentConversation.id);
                if (convIndex !== -1) {
                    // Ensure the local 'conversations' array reflects the added user message
                    // to keep it in sync before the AI response potentially changes it further.
                    conversations[convIndex] = JSON.parse(JSON.stringify(currentConversation));
                    renderHistory(); // Re-render to reflect new message order
                }
            } catch (error) {
                console.error("Failed to save user message to Laravel:", error);
                // Optionally revert local display of user message or mark as unsent
                currentConversation.messages.pop(); // Remove the optimistic user message
                renderCurrentChat(); // Re-render to remove the message
                displayPlaceholder("ERR: FAILED TO SEND MESSAGE. CHECK_CONSOLE.", true);
                enableInput();
                return;
            }

            userInput.value = '';

            // 5. AI Typing Indicator
            const typingMessageDiv = document.createElement('div');
            typingMessageDiv.classList.add('message', 'ai');
            const typingSender = document.createElement('span');
            typingSender.classList.add('sender-label');
            typingSender.textContent = 'SYS:> ';
            typingMessageDiv.appendChild(typingSender);
            const typingDots = document.createElement('span');
            typingDots.classList.add('typing-indicator');
            typingDots.innerHTML = '<span>.</span><span>.</span><span>.</span>';
            typingMessageDiv.appendChild(typingDots);
            messageContainer.appendChild(typingMessageDiv);
            messageContainer.scrollTop = messageContainer.scrollHeight;

            // 6. Call Python Langchain API for AI response
            try {
                console.log(`Sending to Python API (${PYTHON_API_BASE_URL}):target_user_id: , conversation_id=${currentConversation.id}, user_input=${text}`);
                const pythonResponse = await fetch(PYTHON_API_BASE_URL+"/process-chat", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    
                    body: JSON.stringify({
                        user_input: text,
                        conversation_id: currentConversation.id, // This is client_conversation_id
                        target_user_id: {{ $current_user_id }},
                        role: @json($role) // Pass the user role to Python API
                    })
                });

                typingMessageDiv.remove(); // Remove typing indicator

                if (!pythonResponse.ok) {
                    const errorData = await pythonResponse.json().catch(() => ({ message: pythonResponse.statusText }));
                    console.error("Python API Error Data:", errorData);
                    // FastAPI often returns errors in `errorData.detail`
                    throw new Error(`Python API Error ${pythonResponse.status}: ${errorData.detail || errorData.message || 'Unknown error from Python API'}`);
                }

                const responseData = await pythonResponse.json();
                const aiResponseText = responseData.ai_response;
                const links= responseData.links || []; // Optional links from AI response
                // The AI message timestamp is when we receive it from Python and are about to display it
                const aiMessageTimestamp = new Date();

                // Add AI message to local currentConversation object for immediate UI update
                // The Python service has ALREADY saved this AI message to Laravel.
                const aiMessage = { sender: 'ai', text: aiResponseText, timestamp: aiMessageTimestamp };
                currentConversation.messages.push(aiMessage);
                displayMessage('ai', aiResponseText, aiMessageTimestamp); // Display it

                // Update the main 'conversations' list to reflect the new AI message and its timestamp for sorting
                const convIndex = conversations.findIndex(c => c.id === currentConversation.id);
                if (convIndex !== -1) {
                    // conversations[convIndex].messages.push(aiMessage); // Could also do this
                    conversations[convIndex] = JSON.parse(JSON.stringify(currentConversation)); // Deep copy to ensure reactivity
                    renderHistory(); // Re-render history to reflect new message and potential re-sorting
                }

            } catch (error) {
                typingMessageDiv.remove(); // Ensure typing indicator is removed on error
                console.error("Error getting/processing AI response from Python service:", error);
                // Display an error message in the chat interface
                const errorTimestamp = new Date();
                const errorMessage = { sender: 'ai', text: `ERR:SYS_PROCESSING_FAIL (${error.message})`, timestamp: errorTimestamp };
                // Add error to local conversation and display it
                if(currentConversation) { // Check if currentConversation is still valid
                    currentConversation.messages.push(errorMessage);
                    displayMessage('ai', errorMessage.text, errorMessage.timestamp);
                } else { // If no current conversation, show a general placeholder error
                    displayPlaceholder(`ERR:SYS_PROCESSING_FAIL (${error.message})`, true);
                }
                // Optionally, you could try to save this "system error" message to Laravel as an AI response
                // This would require another apiCall to Laravel's message store endpoint
            } finally {
                 if (currentConversation) { // Check if still valid (e.g., not logged out, deleted)
                    enableInput();
                    userInput.focus();
                }
            }
        }


        async function handleDeleteChat(clientConversationId) {
            const conversation = conversations.find(c => c.id === clientConversationId);
            const conversationTitle = conversation ? (conversation.title || `LOG_FILE_${conversation.id.slice(-8)}`) : "UNKNOWN_LOG";

            if (confirm(`DEL_CONFIRM: Delete log "${conversationTitle}"? (Y/N)`)) {
                try {
                    await apiCall(`/web/chat/conversations/${clientConversationId}`, 'DELETE');
                    conversations = conversations.filter(conv => conv.id !== clientConversationId);

                    if (currentConversation && currentConversation.id === clientConversationId) {
                        if (conversations.length > 0) {
                             const sorted = [...conversations].sort((a, b) => {
                               const lmA = a.messages.length > 0 ? new Date(a.messages[a.messages.length-1].timestamp).getTime() : 0;
                               const lmB = b.messages.length > 0 ? new Date(b.messages[b.messages.length-1].timestamp).getTime() : 0;
                               return lmB - lmA;
                            });
                            loadChat(sorted[0].id);
                        } else {
                            startNewUnsavedChat();
                        }
                    } else if (conversations.length === 0) { // If current was not deleted, but list is now empty
                        startNewUnsavedChat();
                    }
                    renderHistory(); // Always re-render history after a delete
                } catch (error) {
                    console.error("Failed to delete conversation:", error);
                    alert("ERR: Could not delete log file from server.");
                }
            }
        }

        newChatBtn.addEventListener('click', startNewUnsavedChat);
        sendBtn.addEventListener('click', handleSendMessage);
        userInput.addEventListener('keypress', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                handleSendMessage();
            }
        });

        if (adminPdfBtn) {
            adminPdfBtn.addEventListener('click', (e) => {
                // If it's an <a> tag and you want to prevent default navigation:
                // e.preventDefault();
                alert('ADMIN_NOTICE: Accessing Knowledge_Source... (Simulated Action, navigation will follow if it is a link)');
            });
        }

        logoutBtn.addEventListener('click', () => {
            if (confirm('LOGOUT_CONFIRM: End current session and logout? (Y/N)')) {
                document.getElementById('logoutForm').submit();
            }
        });

        async function init() {
            disableInput();
            displayPlaceholder("SYSTEM_INIT...LOADING LOGS...");
            await loadConversationsFromServer();

            if (conversations.length > 0) {
                // Server should already sort by recency (updated_at in Laravel query)
                // but we can re-sort client-side by last message timestamp just in case
                // or if server doesn't guarantee perfect order for some reason.
                const sortedByLastMessage = [...conversations].sort((a, b) => {
                    const lastMsgTimestampA = a.messages.length > 0 ? new Date(a.messages[a.messages.length - 1].timestamp).getTime() : 0;
                    const lastMsgTimestampB = b.messages.length > 0 ? new Date(b.messages[b.messages.length - 1].timestamp).getTime() : 0;
                    return lastMsgTimestampB - lastMsgTimestampA;
                });
                loadChat(sortedByLastMessage[0].id);
            } else {
                startNewUnsavedChat();
            }
            // renderHistory() is called by loadChat/startNewUnsavedChat
            // enableInput() and userInput.focus() are called by renderCurrentChat
        }

        init();
    </script>
</body>
</html>