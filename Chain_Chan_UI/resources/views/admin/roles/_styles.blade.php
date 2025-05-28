    <style>
        :root {
            --bg-terminal: #101010;
            --text-green-user: #33cc33;
            --text-green-ai: #22aa22;
            --text-green-secondary: #118811;
            --text-green-placeholder: #116611; /* Added for consistency */
            --border-line: #115511;
            --font-terminal: 'VT323', 'Lucida Console', 'Courier New', monospace;
            --logout-hover-bg: #aa2222;
            --logout-hover-text: #101010;
            --text-red-error: #ff4444;
        }

        @import url('https://fonts.googleapis.com/css2?family=VT323&display=swap');

        body {
            font-family: var(--font-terminal);
            background-color: var(--bg-terminal);
            color: var(--text-green-user);
            margin: 0;
            padding: 20px;
            font-size: 18px;
            line-height: 1.4;
        }

        .container {
            max-width: 900px;
            margin: auto;
            border: 1px solid var(--border-line);
            padding: 20px;
            box-shadow: inset 0 0 10px rgba(34, 170, 34, 0.05);
        }

        h1, h2 {
            color: var(--text-green-user);
            border-bottom: 1px dashed var(--border-line);
            padding-bottom: 10px;
            margin-top: 0;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        h2 {
            font-size: 1.1em;
            margin-top: 25px;
        }

        .button, button {
            background-color: transparent;
            color: var(--text-green-user);
            border: 1px solid var(--border-line);
            padding: 8px 15px;
            text-decoration: none;
            display: inline-block;
            margin: 5px 2px; /* Adjusted margin */
            cursor: pointer;
            font-family: var(--font-terminal);
            font-size: 1em;
            transition: background-color 0.1s, color 0.1s;
        }

        .button:hover, button:hover {
            background-color: var(--text-green-user);
            color: var(--bg-terminal);
        }
        .button:active, button:active {
            background-color: var(--text-green-secondary);
            color: var(--bg-terminal);
        }


        .button-danger {
            border-color: var(--text-red-error);
            color: var(--text-red-error);
        }
        .button-danger:hover {
            background-color: var(--text-red-error);
            color: var(--bg-terminal);
        }
        .button-danger:active {
            background-color: #cc3333; /* Darker red */
            color: var(--bg-terminal);
        }
        button:disabled, .button:disabled { /* For disabled delete button */
            color: var(--text-green-placeholder);
            border-color: var(--text-green-placeholder);
            cursor: not-allowed;
            background-color: transparent !important; /* Ensure no hover effect */
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid var(--border-line);
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #1a1a1a; /* Slightly darker than bg-terminal */
            color: var(--text-green-ai);
            font-weight: normal;
        }

        .alert {
            padding: 10px 15px;
            margin-bottom: 20px;
            border: 1px dashed;
            line-height: 1.3;
        }

        .alert-success {
            color: var(--text-green-user);
            border-color: var(--text-green-user);
            background-color: rgba(51, 204, 51, 0.05);
        }

        .alert-error {
            color: var(--text-red-error);
            border-color: var(--text-red-error);
            background-color: rgba(255, 68, 68, 0.05);
        }
        .alert-error strong {
            display: block;
            margin-bottom: 5px;
        }

        .form-container {
            border: 1px dashed var(--text-green-secondary);
            padding: 20px;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-green-secondary);
        }

        .form-group input[type="text"] {
            background-color: var(--bg-terminal);
            color: var(--text-green-user);
            border: 1px solid var(--border-line);
            padding: 8px 10px;
            width: 100%;
            font-family: var(--font-terminal);
            font-size: 1em;
            box-sizing: border-box; /* Important for width 100% */
        }

        .form-group input[type="text"]:focus {
            outline: none;
            border-color: var(--text-green-user);
            box-shadow: 0 0 5px rgba(51, 204, 51, 0.3);
        }
        .form-group input[readonly] {
            background-color: #181818; /* Slightly darker for readonly */
            color: var(--text-green-placeholder);
            cursor: not-allowed;
        }

        .text-small {
            font-size: 0.85em;
            color: var(--text-green-secondary);
            display: block; /* Make it take its own line */
            margin-top: 3px;
        }

        .actions a, .actions button {
            margin-right: 5px;
            font-size: 0.9em;
            padding: 5px 8px;
        }
        .actions form {
            margin:0; /* Reset margin for inline form */
        }

        .hidden {
            display: none !important; /* Ensure it's hidden */
        }
        .page-header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px; /* Space between H1 and button */
        }
        .content-header {
            padding: 16px 20px; font-size: 1.4em; font-weight: 600; text-align: left;
            color: var(--text-green-user); background-color: #1a1a1a;
            border-bottom: 2px solid var(--text-green-user); flex-shrink: 0;
            margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0, 255, 153, 0.1);
            text-transform: uppercase; letter-spacing: 0.6px;
        }
        table {
    width: 100%;
    border-collapse: collapse;
}
.container {
    max-width: 100%;
    width: 95%;
    margin: 0 auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}



.actions {
    display: flex;
    gap: 10px;
}

    </style>