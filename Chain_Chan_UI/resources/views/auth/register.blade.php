<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register // ChainChan_OS</title>
    <style>
        :root {
            --bg-terminal: #101010;
            --text-green-user: #33cc33;
            --text-green-ai: #22aa22;
            --text-green-secondary: #118811;
            --text-green-placeholder: #116611;
            --border-line: #115511;
            --font-terminal: 'VT323', 'Lucida Console', 'Courier New', monospace;
            --text-red-error: #ff4444;
        }

        @import url('https://fonts.googleapis.com/css2?family=VT323&display=swap');

        html {
            height: 100%;
        }
        body {
            font-family: var(--font-terminal);
            font-size: 18px;
            line-height: 1.4;
            background-color: var(--bg-terminal);
            color: var(--text-green-user);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100%;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .container {
            background-color: var(--bg-terminal);
            padding: 25px 30px;
            border: 2px solid var(--border-line);
            box-shadow: 0 0 15px rgba(34, 170, 34, 0.05), inset 0 0 10px rgba(34, 170, 34, 0.05);
            width: 100%;
            max-width: 450px;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
            color: var(--text-green-user);
            border-bottom: 1px dashed var(--border-line);
            padding-bottom: 10px;
            margin-top: 0;
            margin-bottom: 25px;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-group:last-of-type { /* For the button group */
            margin-bottom: 0;
        }


        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-green-user);
            font-size: 0.9em;
            text-transform: uppercase;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            background-color: transparent;
            border: 1px solid var(--border-line);
            color: var(--text-green-user);
            font-family: var(--font-terminal);
            font-size: 1em;
            outline: none;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--text-green-user);
            box-shadow: 0 0 5px rgba(51, 204, 51, 0.2);
        }
        input::placeholder {
            color: var(--text-green-placeholder);
            opacity: 0.8;
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px 15px;
            background-color: transparent;
            color: var(--text-green-user);
            border: 1px solid var(--border-line);
            font-family: var(--font-terminal);
            font-size: 1em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color 0.1s, color 0.1s;
        }

        button[type="submit"]:hover {
            background-color: var(--text-green-user);
            color: var(--bg-terminal);
            border-color: var(--text-green-user);
        }
        button[type="submit"]:active {
            background-color: var(--text-green-secondary);
            color: var(--bg-terminal);
        }

        .alert {
            padding: 10px;
            border: 1px solid;
            margin-bottom: 20px;
            font-size: 0.9em;
            text-align: left;
        }
        .alert p {
            margin: 0; padding: 0;
        }

        .alert {
            background-color: rgba(255, 68, 68, 0.05);
            color: var(--text-red-error);
            border-color: var(--text-red-error);
        }

        p.auth-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9em;
        }

        p.auth-link a {
            color: var(--text-green-user);
            text-decoration: none;
            border-bottom: 1px dashed var(--text-green-secondary);
            padding-bottom: 1px;
        }

        p.auth-link a:hover {
            color: var(--text-green-ai);
            border-bottom-color: var(--text-green-ai);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register // ChainChan_OS</h2>

        @if ($errors->any())
            <div class="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label for="name">USR_HANDLE:</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="ENTER_DESIRED_HANDLE">
            </div>

            <div class="form-group">
                <label for="email">USR_EMAIL:</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="ENTER_EMAIL_ADDR">
            </div>

            <div class="form-group">
                <label for="password">NEW_PASSWD:</label>
                <input id="password" type="password" name="password" required placeholder="MIN_8_CHARS">
            </div>

            <div class="form-group">
                <label for="password-confirm">CONFIRM_PASSWD:</label>
                <input id="password-confirm" type="password" name="password_confirmation" required placeholder="RETYPE_PASSWD">
            </div>

            <div class="form-group">
                <button type="submit">CREATE_USER_ID</button>
            </div>
        </form>
        <p class="auth-link">ALREADY_REGISTERED? <a href="{{ route('login') }}">ACCESS_LOGIN_PORTAL ></a></p>
    </div>
</body>
</html>