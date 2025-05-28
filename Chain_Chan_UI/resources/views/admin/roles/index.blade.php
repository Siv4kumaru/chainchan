<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Roles // ChainChan_AI</title>
    @include('admin.roles._styles')
</head>
<body>
    @include('auth.sidebar')
    <main class="main-content">
    <h1 class="content-header">Role Management </h1>
    <div class="container">
        <h1>Manage Roles_</h1>
        <div class="page-header-controls">
            <div>
                <a href="{{ route('admin.roles.create') }}" class="button">Add New Role +</a>
                <a href="{{ route('users.index') }}" class="button" style="margin-left: 10px;">< Back to Users</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Name (System)</th>
                    <th>Description</th>
                    <th>Users_Count</th>
                    <th>Actions_</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->description ?: 'N/A' }}</td>
                    <td>{{ $role->users()->count() }}</td> {{-- Assumes users() relationship is set up --}}
{{-- ... inside table ... --}}
<td class="actions">
    {{-- Check if current role is a core role --}}
    @if(in_array($role->name, $coreRoles ?? []))
        <button type="button" class="button" disabled title="SYSTEM_LOCK: Core_roles_cannot_be_fully_edited_from_here.">Edit</button>
    @else
        <a href="{{ route('admin.roles.edit', $role) }}" class="button">Edit</a>
    @endif

    @if(!in_array($role->name, $coreRoles ?? []) && $role->users()->count() == 0)
    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display:inline;" onsubmit="return confirm('DELETE_CONFIRM: Role \'{{ $role->description ?: $role->name }}\'? THIS_ACTION_CANNOT_BE_UNDONE.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="button button-danger">Delete</button>
    </form>
    @else
    <button type="button" class="button button-danger" disabled title="{{ in_array($role->name, $coreRoles ?? []) ? 'SYSTEM_LOCK: Core_roles_cannot_be_deleted.' : 'SYSTEM_LOCK: Cannot_delete_role_with_assigned_users.' }}">Delete</button>
    @endif
</td>
{{-- ... --}}
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 15px;">NO_ROLES_FOUND_IN_SYSTEM.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </main>
</body>
</html>