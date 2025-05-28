<aside class="sidebar">
    <style>
        .sidebar-button.active {
    background-color: #0f0f0f;         /* Dark retro panel */
    color: #00ff7f;                   /* Vintage hacker green */
    font-weight: bold;
    border-left: 3px solid #00ff7f;
    background-color: var(--text-green-user);
    color: var(--bg-terminal);
}
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
            --button-action-bg-hover: #33aa33;
            --button-action-text-hover: #101010;
        }

        @import url('https://fonts.googleapis.com/css2?family=VT323&display=swap');

        html, body {
            height: 100%; margin: 0; padding: 0; overflow: hidden;
            font-family: var(--font-terminal); font-size: 18px; line-height: 1.4;
        }
        body {
            background-color: var(--bg-terminal); color: var(--text-green-user); display: flex;
        }
        .page-container {
            display: flex; width: 100%; height: 100vh; background-color: var(--bg-terminal);
            overflow: hidden; border: 2px solid var(--border-line);
            box-shadow: inset 0 0 10px rgba(34, 170, 34, 0.05);
        }

        /* Sidebar */
        .sidebar {
            width: 250px; min-width: 250px; background-color: var(--bg-terminal);
            color: var(--text-green-user); padding: 15px; box-sizing: border-box;
            display: flex; flex-direction: column; border-right: 1px solid var(--border-line);
        }
        .sidebar h2 {
            font-size: 1.1em; text-transform: uppercase; color: var(--text-green-user);
            margin-top: 0; margin-bottom: 15px; padding-bottom: 5px;
            border-bottom: 1px dashed var(--border-line);
        }
        .sidebar-button {
            background-color: transparent; color: var(--text-green-user);
            border: 1px solid var(--border-line); padding: 10px 12px;
            cursor: pointer; text-align: left; font-size: 1em;
            margin-bottom: 10px; width: 100%;
            transition: background-color 0.1s, color 0.1s;
            font-family: var(--font-terminal); text-decoration: none;
            display: block; box-sizing: border-box;
        }
        .sidebar-button:hover {
            background-color: var(--text-green-user); color: var(--bg-terminal);
        }
        .sidebar-button.logout-button:hover {
             background-color: var(--logout-hover-bg);
             color: var(--logout-hover-text);
             border-color: var(--logout-hover-bg);
        }

        /* Main Content Area */
        .main-content {
            flex-grow: 1; display: flex; flex-direction: column;
            background-color: var(--bg-terminal); overflow: hidden; padding: 5px;
        }
        .content-header {
            padding: 16px 20px; font-size: 1.4em; font-weight: 600; text-align: left;
            color: var(--text-green-user); background-color: #1a1a1a;
            border-bottom: 2px solid var(--text-green-user); flex-shrink: 0;
            margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0, 255, 153, 0.1);
            text-transform: uppercase; letter-spacing: 0.6px;
        }
        .list-container {
            flex-grow: 1; padding: 10px; overflow-y: auto;
            display: flex; flex-direction: column; gap: 5px;
        }
        .list-item {
            padding: 8px 12px;
            border: 1px solid var(--border-line);
            color: var(--text-green-ai);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        .list-item:hover {
            background-color: var(--text-green-secondary);
            color: var(--bg-terminal);
        }
        .list-item .filename {
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .list-item .details {
            white-space: nowrap;
            color: var(--text-green-user);
            font-size: 0.9em;
        }
        .placeholder-message {
            color: var(--text-green-placeholder); text-align: center; margin-top: 20px;
            font-size: 1em; width: 100%;
        }
        .placeholder-message.error { color: var(--text-red-error) !important; }

        .actions-bar {
            padding: 10px;
            border-top: 1px solid var(--border-line);
            display: flex;
            gap: 10px;
        }
        .action-button {
            background-color: transparent; color: var(--text-green-user);
            border: 1px solid var(--border-line); padding: 8px 15px;
            cursor: pointer; font-size: 1em;
            font-family: var(--font-terminal);
            transition: background-color 0.1s, color 0.1s;
        }
        .action-button:hover {
            background-color: var(--button-action-bg-hover);
            color: var(--button-action-text-hover);
            border-color: var(--button-action-bg-hover);
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-terminal); }
        ::-webkit-scrollbar-thumb { background: var(--border-line); border: 1px solid var(--text-green-secondary); }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-green-secondary); }
        ::-webkit-scrollbar-corner { background: var(--bg-terminal); }
    </style>
    <h2>ADMIN_PANEL:</h2>

    @php
        $currentRoute = Route::currentRouteName();
    @endphp
    @if(Route::has('chat.interface'))
    <a href="{{ route('chat.interface') }}" class="sidebar-button {{ $currentRoute === 'chat.interface' ? 'active' : '' }}">< BACK_TO_CHAT</a>
    <a id="adminPdfBtn" href="{{ route('knowledge') }}" class="sidebar-button {{ $currentRoute === 'knowledge' ? 'active' : '' }}">Knowledge_ ></a>
    <a id="manageUsersBtn" href="{{ route('users.index') }}" class="sidebar-button {{ $currentRoute === 'users.index' ? 'active' : '' }}">Manage_Users ></a>
    <a id="manageRolesBtn" href="{{ route('admin.roles.index') }}" class="sidebar-button {{ $currentRoute === 'admin.roles.index' ? 'active' : '' }}">Manage_Roles ></a>
    @else
        <a href="/chat" class="sidebar-button">< BACK_TO_CHAT (Fallback)</a>
    @endif
<div style="margin-top: auto; padding-top: 15px; border-top: 1px dashed var(--border-line); display: flex; flex-direction: column; align-items: flex-start; gap: 10px;">
    <div href="/" class="sidebar-button {{ $currentRoute === 'home' ? 'active' : '' }}" style="font-weight: bold; font-size: 1.1rem;">Chain Chan</div>
    
    @if(Route::has('logout'))
    <form id="logoutFormManage" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <button onclick="document.getElementById('logoutFormManage').submit();" class="sidebar-button logout-button">LOGOUT_SYSTEM</button>
    @endif
</div>

</aside>
