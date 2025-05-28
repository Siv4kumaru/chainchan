<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management // ChainChan_AI</title>
    <style>
        /* --- Retro Terminal Green Vibe - Softer Contrast --- */
        :root {
            --bg-terminal: #101010;
            --text-green-user: #33cc33;
            --text-green-ai: #22aa22;
            --text-green-secondary: #118811;
            --text-green-placeholder: #116611;
            --border-line: #115511;
            --font-terminal: 'VT323', 'Lucida Console', 'Courier New', monospace;
            --logout-hover-bg: #aa2222;
            --logout-hover-text: #101010;
            --text-red-error: #ff4444;
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
            color: var(--text-green-user);
            display: flex;
        }

        .container-main {
            display: flex;
            width: 100%;
            height: 100vh;
            background-color: var(--bg-terminal);
            overflow: hidden;
            border: 2px solid var(--border-line);
            box-shadow: inset 0 0 10px rgba(34, 170, 34, 0.05);
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
            overflow-y: auto;
        }

        .sidebar h2 {
            font-size: 1.1em;
            text-transform: uppercase;
            color: var(--text-green-user);
            margin-top: 20px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed var(--border-line);
        }

        .sidebar-button {
            background-color: transparent;
            color: var(--text-green-user);
            border: 1px solid var(--border-line);
            padding: 10px 12px;
            border-radius: 0;
            cursor: pointer;
            text-align: left;
            font-size: 1em;
            margin-bottom: 10px;
            width: 100%;
            transition: background-color 0.1s, color 0.1s;
            font-family: var(--font-terminal);
            text-decoration: none;
            display: block;
            box-sizing: border-box;
        }
        .sidebar-button:hover {
            background-color: var(--text-green-user);
            color: var(--bg-terminal);
        }
        .sidebar-button.current-page-link {
            background-color: var(--text-green-secondary);
            color: var(--bg-terminal);
            cursor: default;
        }
        .sidebar-button:active:not(.current-page-link) {
            background-color: var(--text-green-secondary);
            color: var(--bg-terminal);
        }
        .sidebar-footer {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px dashed var(--border-line);
        }
        .logout-button:hover {
            background-color: var(--logout-hover-bg);
            color: var(--logout-hover-text);
            border-color: var(--logout-hover-bg);
        }
         .logout-button:active {
            background-color: #881111;
            color: var(--logout-hover-text);
        }

        /* Main Content Area */
        .main-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-terminal);
            overflow: hidden;
            padding: 5px;
        }

        .main-header {
            padding: 16px 20px;
            font-size: 1.4em;
            font-weight: 600;
            color: var(--text-green-user);
            background-color: #1a1a1a;
            border-bottom: 2px solid var(--text-green-user);
            flex-shrink: 0;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .content-panel {
            flex-grow: 1;
            padding: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        /* User List Specific Styles */
        .user-list-item {
            padding: 8px 10px;
            border: 1px solid var(--border-line);
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* cursor: pointer; /* Let child elements handle cursor if needed */
            transition: border-color 0.2s, background-color 0.2s;
        }
        .user-list-item.active { /* Only apply active style, hover might be too much with interactive elements */
            border-color: var(--text-green-user);
            background-color: rgba(34, 170, 34, 0.05);
        }
        .user-list-item .user-info-clickable { /* Make the user info part clickable */
             cursor: pointer;
             flex-grow: 1; /* Allow it to take space */
        }
        .user-list-item .user-prefix {
            color: var(--text-green-secondary);
            margin-right: 5px;
        }
        .user-list-item .user-name {
            color: var(--text-green-user);
            font-weight: bold;
        }


        .no-data-message, .loading-message, .error-message {
            color: var(--text-green-placeholder);
            text-align: center;
            margin: 10px 0;
            font-size: 1em;
            padding: 10px;
            border: 1px dashed var(--text-green-placeholder);
        }
        .success-message { /* For general success messages */
            color: var(--text-green-ai);
            border-color: var(--text-green-ai);
            background-color: rgba(34,170,34,0.05);
        }
        .error-message {
            color: var(--text-red-error);
            border-color: var(--text-red-error);
        }

        /* Conversations & Messages Styles */
        .conversations-wrapper {
            padding-left: 20px;
            margin-top: 5px;
            margin-bottom: 10px;
            border-left: 1px dashed var(--text-green-secondary);
            display: none;
            max-height: 300px;
            overflow-y: auto;
        }
        .conversation-item {
            padding: 6px 8px;
            border: 1px solid var(--border-line);
            margin-bottom: 3px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .conversation-item:hover, .conversation-item.active {
            border-color: var(--text-green-ai);
            background-color: #181818;
        }
        .conversation-title { color: var(--text-green-user); }
        .conversation-meta { color: var(--text-green-secondary); font-size: 0.8em; }

        .messages-wrapper {
            padding-left: 15px;
            margin-top: 3px;
            border-left: 1px dotted var(--border-line);
            display: none;
            max-height: 400px;
            overflow-y: auto;
        }
        .message-item {
            padding: 5px 8px;
            border-bottom: 1px dotted var(--border-line);
            font-size: 0.9em;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .message-item:last-child { border-bottom: none; }
        .message-sender { color: var(--text-green-secondary); text-transform: uppercase; margin-right: 8px; }
        .message-content { color: var(--text-green-user); white-space: pre-wrap; }
        .message-timestamp { color: var(--text-green-placeholder); font-size: 0.75em; margin-left: 10px; float: right; }

        /* Scrollbar styles */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-terminal); }
        ::-webkit-scrollbar-thumb { background: var(--border-line); border: 1px solid var(--text-green-secondary); }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-green-secondary); }
        ::-webkit-scrollbar-corner { background: var(--bg-terminal); }

        /* ROLE MANAGEMENT STYLES */
        .role-management-container {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0; /* Prevent shrinking if user name is long */
        }
        .role-management-container .role-prefix {
            color: var(--text-green-secondary);
        }
        .user-role-select {
            background-color: var(--bg-terminal);
            color: var(--text-green-user);
            border: 1px solid var(--border-line);
            font-family: var(--font-terminal);
            font-size: 0.9em;
            padding: 4px 6px;
            min-width: 150px;
            border-radius: 0;
        }
        .user-role-select:focus {
            outline: none;
            border-color: var(--text-green-user);
            box-shadow: 0 0 3px rgba(51, 204, 51, 0.2);
        }
        .user-role-select:disabled {
            color: var(--text-green-placeholder);
            border-color: var(--text-green-placeholder);
            cursor: not-allowed;
            opacity: 0.7;
        }
        .save-role-btn {
            background-color: var(--text-green-secondary);
            color: var(--bg-terminal);
            border: 1px solid var(--text-green-user);
            padding: 4px 8px;
            font-size: 0.85em;
            cursor: pointer;
            border-radius: 0;
            display: none; /* Initially hidden */
        }
        .save-role-btn:hover {
            background-color: var(--text-green-user);
        }
        .save-role-btn:disabled { /* For when save is in progress */
            background-color: var(--text-green-placeholder);
            color: var(--bg-terminal);
            cursor: not-allowed;
        }
        .role-update-status {
            font-size: 0.8em;
            margin-left: 5px;
            min-width: 80px;
            text-align: left;
            white-space: nowrap;
        }
        .role-update-status.success { color: var(--text-green-ai); }
        .role-update-status.error { color: var(--text-red-error); }
        .role-update-status.saving { color: var(--text-green-placeholder); }

    </style>
</head>
<body>
    <div class="container-main">
        @include('auth.sidebar')

        <main class="main-area">
            <h1 class="main-header">
                USER_MANAGEMENT 
            </h1>

            <div class="content-panel" id="contentPanel">
                @if(session('role_update_success'))
                    <div class="no-data-message success-message">{{ session('role_update_success') }}</div>
                @endif

                @if(session('role_update_error'))
                    <div class="error-message">{{ session('role_update_error') }}</div>
                @endif

                <div class="user-list-container">
                    @if($users->isEmpty())
                        <p class="no-data-message">NO_USERS_FOUND_IN_SYSTEM.</p>
                    @else
                        @foreach($users as $userItem)
                            <div class="user-list-item" data-user-id="{{ $userItem->id }}">
                                <div class="user-info-clickable">
                                    <span class="user-prefix">USR_LOGIN:></span>
                                    <span class="user-name">{{ $userItem->name }}</span>
                                    @if($userItem->id === Auth::id())
                                        <span style="color: var(--text-green-ai); font-size:0.8em; margin-left:5px;">(YOU)</span>
                                    @endif
                                </div>

                                <div class="role-management-container">
                                    <span class="role-prefix">AUTH_LVL:></span>
                                    @php
                                        $disableSelect = false;
                                        $disableTitle = '';

                                        if ($userItem->id === Auth::id() && $userItem->is_only_admin) {
                                            $disableSelect = true;
                                            $disableTitle = 'SYSTEM_LOCK: Cannot_change_role_of_last_administrator.';
                                        }
                                    @endphp

                                    <select class="user-role-select"
                                            name="role_id"
                                            data-user-id="{{ $userItem->id }}"
                                            data-original-role-id="{{ $userItem->role_id ?? '' }}"
                                            {{ $disableSelect ? 'disabled' : '' }}
                                            title="{{ $disableTitle }}">
                                        @foreach($allRoles as $roleId => $roleData)
                                            <option value="{{ $roleData['id'] }}"
                                                {{ (isset($userItem->role) && $userItem->role === $roleData['name']) ? 'selected' : '' }}>
                                                {{ $roleData['name'] }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if(!$disableSelect)
                                        <button class="save-role-btn" data-user-id="{{ $userItem->id }}">SAVE</button>
                                    @endif

                                    <span class="role-update-status" data-user-id="{{ $userItem->id }}"></span>
                                </div>

                                <div class="conversations-wrapper" id="conversations-for-user-{{ $userItem->id }}">
                                    <!-- Conversations loaded here by JS -->
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </main>
    </div>

    <script>
        const newChatBtn = document.getElementById('newChatBtn');
        const adminPdfBtn = document.getElementById('adminPdfBtn'); // Will be null if not admin
        const logoutBtn = document.getElementById('logoutBtn');
        const historyList = document.getElementById('historyList');
        const messageContainer = document.getElementById('messageContainer');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let conversations = []; // Holds { id: client_id, server_id: server_db_id, title: '...', messages: [...] }
        let currentConversation = null; // Holds a single conversation object

        const deleteIconChar = "[DEL]";

        // --- CONFIGURATION FROM LARAVEL (Blade) ---
        // These are passed from your Laravel ChatController@index method to the view
        const CURRENT_USER_ID = {{ Js::from($current_user_id ?? null) }}; // Laravel 9+ Js facade for safe JS output
        const PYTHON_API_URL = {{ Js::from($python_api_url ?? 'http://localhost:8001') }}; // Default if not passed

        if (CURRENT_USER_ID === null) {
            console.error("FATAL: CURRENT_USER_ID not passed from Laravel to JavaScript. Chat cannot function correctly.");
            alert("System Error: User identity missing. Please refresh or contact support.");
        }
        console.log("JS Config: CURRENT_USER_ID =", CURRENT_USER_ID, "PYTHON_API_URL =", PYTHON_API_URL);


        // --- API Helper for Laravel API Calls (from JS to Laravel) ---
        async function laravelApiCall(url, method = 'GET', body = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken, // For session-based Laravel routes
                    'Accept': 'application/json'
                }
            };
            if (body) {
                options.body = JSON.stringify(body);
            }
            try {
                const response = await fetch(url, options); // Assumes relative URLs for Laravel calls
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: response.statusText }));
                    console.error(`Laravel API Error (${url}) ${response.status}: ${errorData.message || 'Unknown error'}`, errorData);
                    throw new Error(`Laravel API Error ${response.status}: ${errorData.message || 'Unknown error'}`);
                }
                if (response.status === 204) return null; // No content
                return response.json();
            } catch (error) {
                console.error(`Laravel API call to ${url} failed:`, error);
                throw error; // Re-throw to be caught by caller
            }
        }

        function generateClientId() {
            return `client_${Date.now().toString(16).toUpperCase()}_${Math.random().toString(16).slice(2)}`;
        }

        function parseServerConversations(serverData) {
            if (!Array.isArray(serverData)) {
                console.error("parseServerConversations: serverData is not an array", serverData);
                return [];
            }
            return serverData.map(conv => ({
                id: conv.id,
                server_id: conv.server_id,
                title: conv.title,
                messages: Array.isArray(conv.messages) ? conv.messages.map(msg => ({
                    sender: msg.sender,
                    text: msg.text,
                    timestamp: msg.timestamp ? new Date(msg.timestamp) : new Date()
                })) : [],
                isUnsaved: false
            }));
        }

        async function loadConversationsFromServer() {
            try {
                // These URLs are for JS to Laravel communication (session-based auth)
                // Ensure these routes exist in your routes/web.php and are protected by 'auth' middleware
                const serverData = await laravelApiCall('/web/chat/conversations'); // Example path, adjust to your web routes
                conversations = parseServerConversations(serverData);
            } catch (error) {
                console.error("Failed to load conversations from Laravel:", error);
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
                const lastMsgA = a.messages.length > 0 ? new Date(a.messages[a.messages.length - 1].timestamp).getTime() : 0;
                const lastMsgB = b.messages.length > 0 ? new Date(b.messages[b.messages.length - 1].timestamp).getTime() : 0;
                return lastMsgB - lastMsgA;
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
            if (!dateObj || !(dateObj instanceof Date) || isNaN(dateObj.getTime())) return '[??:??:??]';
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
            messageContent.textContent = text;
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
            if (document.activeElement !== userInput) { // Avoid stealing focus if user clicked elsewhere
                userInput.focus();
            }
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
                console.warn(`Chat with client ID ${clientConversationId} not found locally. Starting new one.`);
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

        async function ensureConversationExistsOnServer(conv) { // For JS to Laravel
            if (conv.isUnsaved || !conv.server_id) {
                try {
                    // This URL should match a route in your routes/web.php
                    const newServerConv = await laravelApiCall('/web/chat/conversations', 'POST', {
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
                    renderHistory();
                    return true;
                } catch (error) {
                    console.error("Failed to create conversation on Laravel server:", error);
                    displayPlaceholder("ERR: COULD NOT SAVE SESSION. TRY AGAIN.", true);
                    return false;
                }
            }
            return true;
        }

        async function updateConversationTitleOnServer(conv, newTitle) { // For JS to Laravel
            if (!conv.server_id) {
                console.warn("Cannot update title for unsaved conversation on server.");
                return;
            }
            try {
                 // This URL should match a route in your routes/web.php
                await laravelApiCall(`/web/chat/conversations/${conv.id}/title`, 'PUT', { title: newTitle });
                conv.title = newTitle;
                const convInList = conversations.find(c => c.id === conv.id);
                if (convInList) convInList.title = newTitle;
                renderHistory();
            } catch (error) {
                console.error("Failed to update conversation title on Laravel server:", error);
            }
        }

        async function handleSendMessage() {
            const text = userInput.value.trim();
            if (text === '' || !currentConversation || userInput.disabled) return;

            if (CURRENT_USER_ID === null) {
                console.error("Error: Current User ID is not available. Cannot send message.");
                displayMessage('ai', "SYSTEM ERROR: User context missing. Please refresh.", new Date());
                return;
            }

            userInput.disabled = true;
            sendBtn.disabled = true;
            const userMessageTimestamp = new Date();

            const canProceed = await ensureConversationExistsOnServer(currentConversation);
            if (!canProceed) {
                enableInput();
                return;
            }

            if (currentConversation.messages.length === 0 && currentConversation.title.startsWith('SESS_')) {
                const newTitle = `LOG_${text.substring(0, 15).trim().replace(/\s+/g, '_')}`;
                await updateConversationTitleOnServer(currentConversation, newTitle);
            }

            const userMessage = { sender: 'user', text: text, timestamp: userMessageTimestamp };
            currentConversation.messages.push(userMessage);
            displayMessage('user', text, userMessageTimestamp);

            try {
                // Save user message to Laravel (this URL should match a route in routes/web.php)
                await laravelApiCall(`/web/chat/conversations/${currentConversation.id}/messages`, 'POST', {
                    ...userMessage,
                    timestamp: userMessageTimestamp.toISOString()
                });
                const convIndex = conversations.findIndex(c => c.id === currentConversation.id);
                if (convIndex !== -1) {
                    conversations[convIndex] = JSON.parse(JSON.stringify(currentConversation));
                    renderHistory();
                }
            } catch (error) {
                console.error("Failed to save user message to Laravel:", error);
                currentConversation.messages.pop(); // Revert optimistic update
                renderCurrentChat();
                displayPlaceholder("ERR: FAILED TO SEND MESSAGE. CHECK_CONSOLE.", true);
                enableInput();
                return;
            }

            userInput.value = '';

            const typingMessageDiv = document.createElement('div');
            typingMessageDiv.classList.add('message', 'ai', 'typing-indicator-container'); // Added a class for easier targeting
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

            try {
                const pythonApiEndpoint = `${PYTHON_API_URL}/process-chat`; // Construct full URL
                console.log(`JS: Sending to Python API (${pythonApiEndpoint}): conv_id=${currentConversation.id}, input=${text.substring(0,50)}..., target_user_id=${CURRENT_USER_ID}`);

                const pythonResponse = await fetch(pythonApiEndpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        user_input: text,
                        conversation_id: currentConversation.id,
                        target_user_id: CURRENT_USER_ID
                    })
                });

                // Always remove typing indicator once fetch completes (success or error)
                const activeTypingIndicator = messageContainer.querySelector('.message.ai.typing-indicator-container');
                if (activeTypingIndicator) activeTypingIndicator.remove();


                if (!pythonResponse.ok) {
                    let errorDetail = `Python API Error ${pythonResponse.status}`;
                    try {
                        const errorData = await pythonResponse.json();
                        errorDetail += `: ${errorData.detail || errorData.message || pythonResponse.statusText}`;
                        console.error("Python API Error Data:", errorData);
                    } catch (e) {
                        errorDetail += `: ${pythonResponse.statusText}`;
                        console.error("Python API Error: Could not parse error response as JSON.");
                    }
                    throw new Error(errorDetail);
                }

                const responseData = await pythonResponse.json();
                const aiResponseText = responseData.ai_response;
                const aiMessageTimestamp = new Date();

                const aiMessage = { sender: 'ai', text: aiResponseText, timestamp: aiMessageTimestamp };
                currentConversation.messages.push(aiMessage);
                displayMessage('ai', aiResponseText, aiMessageTimestamp);

                const convIndex = conversations.findIndex(c => c.id === currentConversation.id);
                if (convIndex !== -1) {
                    conversations[convIndex] = JSON.parse(JSON.stringify(currentConversation));
                    renderHistory();
                }

            } catch (error) {
                console.error("Error getting/processing AI response from Python service:", error);
                const activeTypingIndicator = messageContainer.querySelector('.message.ai.typing-indicator-container'); // Ensure removal on error too
                if (activeTypingIndicator) activeTypingIndicator.remove();

                const errorTimestamp = new Date();
                const errorMessageText = `ERR:SYS_PROCESSING_FAIL (${error.message || 'Unknown Python API communication error'})`;
                if(currentConversation) {
                    currentConversation.messages.push({ sender: 'ai', text: errorMessageText, timestamp: errorTimestamp });
                    displayMessage('ai', errorMessageText, errorTimestamp);
                } else {
                    displayPlaceholder(errorMessageText, true);
                }
            } finally {
                 if (currentConversation) {
                    enableInput();
                    userInput.focus();
                }
            }
        }

        async function handleDeleteChat(clientConversationId) { // For JS to Laravel
            const conversation = conversations.find(c => c.id === clientConversationId);
            const conversationTitle = conversation ? (conversation.title || `LOG_FILE_${conversation.id.slice(-8)}`) : "UNKNOWN_LOG";

            if (confirm(`DEL_CONFIRM: Delete log "${conversationTitle}"? (Y/N)`)) {
                try {
                    // This URL should match a route in your routes/web.php
                    await laravelApiCall(`/web/chat/conversations/${clientConversationId}`, 'DELETE');
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
                    } else if (conversations.length === 0) {
                        startNewUnsavedChat();
                    }
                    renderHistory();
                } catch (error) {
                    console.error("Failed to delete conversation from Laravel:", error);
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

        if (adminPdfBtn) { // For admin panel link
            adminPdfBtn.addEventListener('click', (e) => {
                // alert('ADMIN_NOTICE: Accessing Knowledge_Source...'); // Not needed if it's just a link
            });
        }

        logoutBtn.addEventListener('click', () => {
            if (confirm('LOGOUT_CONFIRM: End current session and logout? (Y/N)')) {
                document.getElementById('logoutForm').submit();
            }
        });

        async function init() {
            if (CURRENT_USER_ID === null) {
                displayPlaceholder("SYSTEM ERROR: USER IDENTITY NOT AVAILABLE. CANNOT INITIALIZE CHAT.", true);
                disableInput();
                return;
            }
            disableInput();
            displayPlaceholder("SYSTEM_INIT...LOADING LOGS...");
            await loadConversationsFromServer();

            if (conversations.length > 0) {
                const sortedByLastMessage = [...conversations].sort((a, b) => {
                    const lastMsgTimestampA = a.messages.length > 0 ? new Date(a.messages[a.messages.length - 1].timestamp).getTime() : 0;
                    const lastMsgTimestampB = b.messages.length > 0 ? new Date(b.messages[b.messages.length - 1].timestamp).getTime() : 0;
                    return lastMsgTimestampB - lastMsgTimestampA;
                });
                loadChat(sortedByLastMessage[0].id);
            } else {
                startNewUnsavedChat();
            }
        }

        init();
    </script>
</body>
</html>