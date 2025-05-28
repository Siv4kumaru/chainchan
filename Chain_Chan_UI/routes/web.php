<?php
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController; 
use App\Http\Controllers\KnowledgeManagementController;


// Welcome page - redirect to login or chat
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('chat.interface');
    }
    return redirect()->route('login');
});

// Authentication routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated routes without admin middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.interface');
    Route::prefix('web/chat')->name('web.chat.')->group(function () {
        Route::get('/conversations', [ChatController::class, 'getConversations'])->name('conversations.index');
        Route::post('/conversations', [ChatController::class, 'storeConversation'])->name('conversations.store');
        Route::put('/conversations/{conversation_client_id}/title', [ChatController::class, 'updateConversationTitle'])->name('conversations.title.update');
        Route::delete('/conversations/{conversation_client_id}', [ChatController::class, 'deleteConversation'])->name('conversations.destroy');
        Route::get('/conversations/{conversation_client_id}/messages', [ChatController::class, 'getConversationMessages'])->name('conversations.messages.index');
        Route::post('/conversations/{conversation_client_id}/messages', [ChatController::class, 'storeMessage'])->name('messages.store');
    });
});

// Admin-only routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/knowledge', [AuthController::class, 'admin_only'])->name('knowledge'); // moved inside admin middleware group
    Route::get('/admin/users/{user}/conversations', [UserController::class, 'getUserConversations'])->name('admin.users.conversations');
    Route::get('/admin/conversations/{conversation}/messages', [UserController::class, 'getConversationMessages'])->name('admin.conversations.messages');

    Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('roles', RoleController::class);

    // Add this for updating user roles via AJAX
    Route::post('/users/{user}/update-role', [UserController::class, 'updateUserRole'])->name('users.updateRole');

});
Route::get('/get-roles', [KnowledgeManagementController::class, 'getRoles'])->name('knowledge.roles.list');
// Full name: knowledgemgmt.assignments
Route::get('/get-assignments', [KnowledgeManagementController::class, 'getKnowledgeSourceAssignments'])->name('knowledge.assignments');
// Full name: knowledgemgmt.updateAssignments
// Route::post('/update-assignments', [KnowledgeManagementController::class, 'updateKnowledgeSourceAssignments'])->name('knowledge.updateAssignments');
    Route::patch('/admin/users/{user}/role', [UserController::class, 'updateUserRole'])->name('admin.users.updateRole');

});

//role


