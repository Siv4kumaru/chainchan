<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Role // {{ $role->name }} // ChainChan_AI</title>
    @include('admin.roles._styles')
</head>
<body>
    <div class="container form-container">
        <h1>Edit Role_: {{ $role->description ?: $role->name }}</h1>

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

        <form action="{{ route('admin.roles.update', $role) }}" method="POST"> {{-- $role will use roleid --}}
            @csrf
            @method('PUT')
{{-- ... inside form ... --}}
<div class="form-group">
    <label for="name">Role Name (System_ID):</label>
    <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" required
           {{-- Use $coreRoles passed from controller --}}
           {{ (isset($coreRoles) && in_array($role->name, $coreRoles)) ? 'readonly title="SYSTEM_LOCK: Name_of_core_roles_cannot_be_changed."' : '' }}>
    <p class="text-small">Use_only_lowercase_letters_numbers_underscores. This_is_used_internally.</p>
     @if(isset($coreRoles) && in_array($role->name, $coreRoles))
        <p class="text-small" style="color:var(--text-red-error);">SYSTEM_ALERT: Name_of_core_roles_cannot_be_changed.</p>
    @endif
</div>
{{-- ... --}}
            <div class="form-group">
                <label for="description">Description (User-Friendly):</label>
                <input type="text" id="description" name="description" value="{{ old('description', $role->description) }}">
                <p class="text-small">A_user-friendly_description. Optional.</p>
            </div>
            <button type="submit" class="button">Update Role</button>
            <a href="{{ route('admin.roles.index') }}" class="button">Cancel</a>
        </form>
    </div>
</body>
</html>