<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\KnowledgeSourceRole; // Make sure this model exists
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View; // For type hinting view responses

class KnowledgeManagementController extends Controller
{
    // If you want to protect these routes with auth middleware,
    // you can add it in the constructor or on the routes themselves.
    // public function __construct()
    // {
    //     $this->middleware('auth'); // Example: requires login for all methods
    // }

    /**
     * Display the main page for managing knowledge PDF sources.
     */
    public function index(): View
    {
        // You need to pass the Python API base URL to the view.
        // Get this from your config or .env
        $python_api_base_url = config('services.python_api.url', 'http://localhost:YOUR_PYTHON_PORT'); // Example

        return view('auth.knowledge', ['python_api' => $python_api_base_url]);
    }

    /**
     * Get all roles for frontend dropdowns.
     * (Replaces the old api.roles.list)
     */
    public function getRoles(): JsonResponse
    {
        try {
            $roles = Role::orderBy('name')->get(['id', 'name', 'description']);
            return response()->json($roles);
        } catch (\Exception $e) {
            Log::error("Error fetching roles: " . $e->getMessage());
            return response()->json(['message' => 'ERROR_FETCHING_ROLES', 'error_detail' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all current assignments of PDF sources to roles.
     * Returns a map of source_identifier => array of assigned role objects [{id, name}].
     * (Replaces the old api.knowledge.assignments)
     */
    public function getKnowledgeSourceAssignments(): JsonResponse
    {
        try {
            $assignmentsRaw = KnowledgeSourceRole::with('role:id,name')
                                              ->orderBy('source_identifier')
                                              ->get();

            $groupedAssignments = [];
            foreach ($assignmentsRaw as $assignment) {
                if (!isset($groupedAssignments[$assignment->source_identifier])) {
                    $groupedAssignments[$assignment->source_identifier] = [];
                }
                if ($assignment->role) { // Ensure role exists after join
                    $groupedAssignments[$assignment->source_identifier][] = [
                        'id' => $assignment->role->id,
                        'name' => $assignment->role->name,
                    ];
                }
            }
            return response()->json($groupedAssignments);
        } catch (\Exception $e) {
            Log::error("Error fetching knowledge source assignments: " . $e->getMessage());
            return response()->json(['message' => 'ERROR_FETCHING_ASSIGNMENTS', 'error_detail' => $e->getMessage()], 500);
        }
    }

    /**
     * Update role assignments for a given PDF source identifier.
     * Expects an array of role_ids.
     * (Replaces the old api.knowledge.updateAssignments)
     */
    public function updateKnowledgeSourceAssignments(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'source_identifier' => 'required|string|max:255',
            'role_ids' => 'present|array',
            'role_ids.*' => 'integer|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sourceIdentifier = $request->input('source_identifier');
        $newRoleIds = $request->input('role_ids', []);

        DB::beginTransaction();
        try {
            // 1. Delete existing roles for this source that are not in the new list
            KnowledgeSourceRole::where('source_identifier', $sourceIdentifier)
                               ->whereNotIn('role_id', $newRoleIds)
                               ->delete();

            // 2. Add new roles
            $existingRoleIdsForSource = KnowledgeSourceRole::where('source_identifier', $sourceIdentifier)
                                                           ->pluck('role_id')
                                                           ->all();
            $rolesToInsert = [];
            foreach ($newRoleIds as $roleId) {
                if (!in_array($roleId, $existingRoleIdsForSource)) {
                    $rolesToInsert[] = [
                        'source_identifier' => $sourceIdentifier,
                        'role_id' => $roleId,
                        // Add timestamps if your model uses them
                        // 'created_at' => now(),
                        // 'updated_at' => now(),
                    ];
                }
            }

            if (!empty($rolesToInsert)) {
                KnowledgeSourceRole::insert($rolesToInsert);
            }

            DB::commit();

            $updatedRoles = Role::whereIn('id', $newRoleIds)->get(['id', 'name'])->toArray();

            return response()->json([
                'message' => 'ROLES_ASSIGNED_SUCCESSFULLY',
                'source_identifier' => $sourceIdentifier,
                'assigned_roles' => $updatedRoles,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating role assignments for knowledge source: ' . $e->getMessage(), [
                'source' => $sourceIdentifier,
                'role_ids' => $newRoleIds,
                'exception' => $e
            ]);
            return response()->json(['message' => 'ERROR_UPDATING_ASSIGNMENTS', 'error_detail' => $e->getMessage()], 500);
        }
    }
}