<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Models\User;



class ChatController extends Controller
{
    // Helper to check if the authenticated user is the service account
    private function isServiceAccount(User $authenticatedUser = null): bool
    {
        // $user = $authenticatedUser ?: Auth::user();
        // return $user && $user->email === 'python.service@example.com'; // Adjust email if different
        return True;
    }

public function getMessagesForTargetUser(Request $request, int $target_user_id, string $conversation_client_id)
{
    $targetUser = User::find($target_user_id);
    if (!$targetUser) {
        return response()->json(['error' => 'Target user not found.'], 404);
    }

    $conversation = $targetUser->conversations()
        ->where('client_conversation_id', $conversation_client_id)
        ->with(['messages' => function ($query) {
            $query->orderBy('created_at', 'asc')->limit(5); // limit to 10 messages
        }])
        ->first();

    if (!$conversation) {
        return response()->json(['error' => 'Conversation not found for the target user.'], 404);
    }

    return response()->json($conversation->messages->map(function ($msg) {
        return [
            'sender' => $msg->sender,
            'text' => $msg->text,
            'timestamp' => $msg->created_at ? $msg->created_at->toIso8601String() : null,
        ];
    }));
}


    public function storeAiMessageForTargetUser(Request $request, int $target_user_id, string $conversation_client_id)
    {

        
        $validated = $request->validate([
            'sender' => 'required|in:ai', // Python service should ONLY send 'ai' messages
            'text' => 'required|string'
        ]);

        $targetUser = User::find($target_user_id);
        if (!$targetUser) {
            return response()->json(['error' => 'Target user not found.'], 404);
        }

        $conversation = $targetUser->conversations()
            ->where('client_conversation_id', $conversation_client_id)
            ->first();

        if (!$conversation) {
            // Option: If Python should be able to create a conversation if it doesn't exist for the target user
            // This requires more logic, e.g., getting a title for the new conversation.
            // For now, let's assume the conversation MUST exist (created by the JS frontend).
            return response()->json(['error' => 'Conversation not found for the target user. AI message cannot be stored.'], 404);
        }

        $message = $conversation->messages()->create([
            'sender' => $validated['sender'], // Should be 'ai'
            'text' => $validated['text'],
            'client_timestamp' => now(), // Use current time for AI messages
            // 'user_id' could be null for AI messages, or you could set it to Auth::id() (the service account ID)
            // to track which system generated the message.
        ]);

        $conversation->touch(); // Update conversation's updated_at timestamp

        return response()->json([
            'id' => $message->id, // Server message ID
            'sender' => $message->sender,
            'text' => $message->text,
            'timestamp' => $message->created_at ? $message->created_at->toIso8601String() : null,
        ], 201);
    }
public function getConversationMessages(Request $request, $conversation_client_id)
{
    $user = Auth::user();

    $conversation = $user->conversations()
                         ->where('client_conversation_id', $conversation_client_id)
                         ->firstOrFail();
            

    $messages = Message::select('sender', 'text', 'created_at as timestamp')
                    ->where('conversation_id', $conversation->id)
                    ->orderBy('created_at', 'asc')
                       ->get();
    return response()->json($messages);
}
    public function index()
    {

        // The main chat view will be returned.
        // Initial conversations can be loaded via API by the JS.
        $python_api = config('custom.python_api');
        $api_token = config('custom.api_token'); // If you need an API token for the Python service
        // dd(Auth::id())
        return view('welcome', [
            'current_user_id' => Auth::id(),
            'role' => Auth::user()->role, // Pass the user's role to the view
            'python_api' => $python_api, // Get from config or .env
        ]);
    }

    public function getConversations(Request $request)
    {
        $user = Auth::user();
        $conversations = $user->conversations()
                              ->with('messages') // Eager load messages
                              ->orderBy('created_at', 'desc') // Or created_at
                              ->get();
        // Transform to match JS expectations if necessary
        return response()->json($conversations->map(function ($conv) {
            return [
                'id' => $conv->client_conversation_id, // Use client_id for JS
                'server_id' => $conv->id, // Keep server_id for API calls
                'title' => $conv->title,
                'messages' => $conv->messages->map(function ($msg) {
                    return [
                        'sender' => $msg->sender,
                        'text' => $msg->text,
                        'timestamp' => $msg->created_at ? $msg->created_at->toIso8601String() : null,
                    ];
                }),
                // Add any other fields your JS expects, like 'isUnsaved' (always false from server)
            ];
        }));
    }

    public function storeConversation(Request $request)
    {
        $request->validate([
            'client_conversation_id' => 'required|string|unique:conversations,client_conversation_id',
            'title' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $conversation = $user->conversations()->create([
            'client_conversation_id' => $request->client_conversation_id,
            'title' => $request->title,
        ]);

        return response()->json([
            'id' => $conversation->client_conversation_id, // JS expects this as 'id'
            'server_id' => $conversation->id,
            'title' => $conversation->title,
            'messages' => [] // New conversation has no messages yet
        ], 201);
    }

    public function updateConversationTitle(Request $request, $conversation_client_id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $conversation = $user->conversations()->where('client_conversation_id', $conversation_client_id)->firstOrFail();

        $conversation->update(['title' => $request->title]);

        return response()->json([
            'id' => $conversation->client_conversation_id,
            'server_id' => $conversation->id,
            'title' => $conversation->title
        ]);
    }


    public function storeMessage(Request $request, $conversation_client_id)
    {
        $request->validate([
            'sender' => 'required|in:user,ai',
            'text' => 'required|string',
            'timestamp' => 'required|date_format:Y-m-d\TH:i:s.v\Z', // ISO 8601 from JS new Date().toISOString()
        ]);

        $user = Auth::user();
        $conversation = $user->conversations()->where('client_conversation_id', $conversation_client_id)->firstOrFail();

        $message = $conversation->messages()->create([
            'sender' => $request->sender,
            'text' => $request->text,
            'client_timestamp' => $request->timestamp,
        ]);
        
        // Touch conversation to update its updated_at timestamp
        $conversation->touch();


        return response()->json([
            'id' => $message->id, // Server message ID
            'sender' => $message->sender,
            'text' => $message->text,
            'timestamp' => $message->client_timestamp ? $message->client_timestamp->toIso8601String() : null,
        ], 201);
    }

    public function deleteConversation(Request $request, $conversation_client_id)
    {
        $user = Auth::user();
        $conversation = $user->conversations()->where('client_conversation_id', $conversation_client_id)->firstOrFail();

        $conversation->delete(); // This will also delete related messages due to onDelete('cascade')

        return response()->json(null, 204);
    }
}

