<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login // ChainChan_OS</title>
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
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--text-green-user);
            box-shadow: 0 0 5px rgba(51, 204, 51, 0.2);
        }
        input::placeholder {
            color: var(--text-green-placeholder);
            opacity: 0.8;
        }

        .remember-me-group {
            display: flex;
            align-items: center;
        }
        .remember-me-group input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            background-color: transparent;
            border: 1px solid var(--border-line);
            padding: 7px;
            display: inline-block;
            position: relative;
            vertical-align: middle;
            margin-right: 8px;
            cursor: pointer;
            margin-bottom: 0;
        }
        .remember-me-group input[type="checkbox"]:checked {
            background-color: var(--text-green-user);
            border-color: var(--text-green-user);
        }
        .remember-me-group input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 4px; /* Adjust for visual centering */
            top: 1px;  /* Adjust for visual centering */
            width: 3px;
            height: 7px;
            border: solid var(--bg-terminal);
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        .remember-me-group label[for="remember"] {
            text-transform: uppercase;
            font-size: 0.9em;
            margin-bottom: 0;
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
            margin-top: 5px; /* Little space above button if remember me is there */
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

        .alert, .status {
            padding: 10px;
            border: 1px solid;
            margin-bottom: 20px;
            font-size: 0.9em;
            text-align: left; /* Keep text aligned left for readability */
        }
        .alert p, .status p {
            margin: 0; padding: 0;
        }

        .alert {
            background-color: rgba(255, 68, 68, 0.05);
            color: var(--text-red-error);
            border-color: var(--text-red-error);
        }

        .status {
            background-color: rgba(51, 204, 51, 0.05);
            color: var(--text-green-user);
            border-color: var(--text-green-user);
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
        <h2>Login // ChainChan_OS</h2>

        @if (session('status'))
            <div class="status">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">USR_EMAIL:</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="ENTER_EMAIL_ADDR">
            </div>

            <div class="form-group">
                <label for="password">USR_PASSWD:</label>
                <input id="password" type="password" name="password" required placeholder="ENTER_PASSWD">
            </div>

            <div class="form-group remember-me-group">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">REMEMBER_DEVICE</label>
            </div>

            <div class="form-group">
                <button type="submit">ACCESS_SYSTEM</button>
            </div>
        </form>
        <p class="auth-link">NO_ACCOUNT? <a href="{{ route('register') }}">INITIATE_REGISTRATION ></a></p>
    </div>
</body>
</html>