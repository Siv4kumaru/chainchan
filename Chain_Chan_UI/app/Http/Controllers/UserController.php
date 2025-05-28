<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation; // Make sure you have this model
use App\Models\Message;      // And this model
use App\Models\User; // Make sure you have your User model imported
use Illuminate\Support\Facades\Auth; // For authentication checks
use App\Models\Role; // Import the Role model
use Illuminate\Validation\Rule; // For validation rules

class UserController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('chat.interface')->with('error', 'Unauthorized access to user management.');
        }
        
        $users = User::select("id","name","email","roleid","role",)->orderBy('name')->get();
        $allRoles = Role::select('id', 'name', 'description')->orderBy('name')->get();




        // dd($users,$allRoles);
        return view('users.index', compact('users', 'allRoles')); // Pass enrichedUsers
    }
       public function updateUserRole(Request $request, User $user)
    {
 
        
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'roleid' => ['required', 'integer', Rule::exists('roles', 'id')], // Validate against roleid
        ]);

        $newRoleId = $validated['roleid'];
        $selectedRole = Role::where('id', $newRoleId)->first();

        if (!$selectedRole) {
            return response()->json(['error' => 'Selected role not found.'], 404);
        }

        // Prevent admin from demoting themselves if they are the only admin
        // This check uses the 'role' (name string) column for simplicity of the check
        if ($user->id === Auth::id() && $user->role === 'admin' && $selectedRole->name !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json(['error' => 'SYSTEM_LOCK: Cannot_remove_the_last_administrator_role.'], 422);
            }
        }
        // Optional: Prevent changing role of a super admin (e.g., user ID 1) from 'admin'
        if ($user->id === 1 && $user->role === 'admin' && $selectedRole->name !== 'admin') {
             return response()->json(['error' => 'SYSTEM_LOCK: Cannot_change_role_of_primary_administrator.'], 422);
        }


        // Update both role_id and the role name string
        $user->roleid = $selectedRole->id;
        $user->role = $selectedRole->name; 
        $user->save();

return response()->json([
    "message" => "User role updated successfully.",
]);


    }
        public function getUserConversations(User $user)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Log::info("Admin fetching conversations for User ID: {$user->id}");

        $conversations = $user->conversations()
                              ->withCount('messages') // Efficiently get message count
                              ->orderBy('updated_at', 'desc')
                              ->get();

        // Transform data to match what the admin JS might expect.
        // Your ChatController uses 'client_conversation_id' as 'id' for its JS.
        // For admin, we can use the actual server DB ID for clarity in API calls.
        return response()->json($conversations->map(function ($conv) {
            return [
                'id' => $conv->id, // This is the conversation's actual DB ID
                'client_conversation_id' => $conv->client_conversation_id, // Good to have for reference
                'title' => $conv->title ?: "CONVO_CLIENT_#{$conv->client_conversation_id}", // Fallback title
                'updated_at' => $conv->updated_at ? $conv->updated_at->toIso8601String() : null,
                'messages_count' => $conv->messages_count, // From withCount
            ];
        }));
    }

    /**
     * Get messages for a specific conversation (admin action).
     * We'll use the Conversation model route binding (using its server ID).
     */
    public function getConversationMessages(Conversation $conversation)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Log::info("Admin fetching messages for Conversation (DB ID): {$conversation->id}");

        // You might want to ensure this conversation belongs to the user being inspected,
        // or rely on the admin having global access.

        $messages = $conversation->messages()
                                 ->orderBy('client_timestamp', 'asc') // Or 'created_at'
                                 ->get(); // Select specific fields if needed: ->get(['sender', 'text', 'client_timestamp'])

        // Transform data: Your ChatController uses 'text' and 'client_timestamp'.
        // The admin JS from previous example used 'content' and 'timestamp'. Let's map.
        return response()->json($messages->map(function ($msg) {
            return [
                'id' => $msg->id, // Message's DB ID
                'sender' => $msg->sender,
                'content' => $msg->text, // Map 'text' to 'content' for the admin JS
                'timestamp' => $msg->client_timestamp ? $msg->client_timestamp->toIso8601String() : ($msg->created_at ? $msg->created_at->toIso8601String() : null),
            ];
        }));
    }
}