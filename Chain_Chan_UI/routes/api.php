<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController; // Make sure to import your controller

Route::get("/hi", [function () {
    return "hi";
}]);

Route::get('/chat-services/users/{target_user_id}/conversations/{conversation_client_id}/messages',[ChatController::class, 'getMessagesForTargetUser']);
Route::post('/chat-services/users/{target_user_id}/conversations/{conversation_client_id}/messages',[ChatController::class, 'storeAiMessageForTargetUser']);
