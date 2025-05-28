<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Role // ChainChan_AI</title>
    @include('admin.roles._styles')
</head>
<body>
    <div class="container form-container">
        <h1>Create New Role_</h1>

        @if($errors->any())
            <div class="alert alert-error">
                <strong>INPUT_ERRORS_DETECTED:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Role Name (System_ID):</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                <p class="text-small">Use_only_lowercase_letters_numbers_underscores (e.g., 'content_editor'). This_is_used_internally.</p>
            </div>
            <div class="form-group">
                <label for="description">Description (User-Friendly):</label>
                <input type="text" id="description" name="description" value="{{ old('description') }}">
                <p class="text-small">A_user-friendly_description (e.g., 'Content Editor - can create and edit posts.'). Optional.</p>
            </div>
            <button type="submit" class="button">Create Role</button>
            <a href="{{ route('admin.roles.index') }}" class="button">Cancel</a>
        </form>
    </div>
</body>
</html>