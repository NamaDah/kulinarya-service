<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return response()->json($users);
    }

    /**
     * Update the user's role.
     */
    public function updateRole(Request $request, User $user): JsonResponse
    {
        // Prevent deleting or modifying the last admin if we wanted to be perfectly safe, 
        // but for now, just allow toggling.
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'You cannot change your own role.'], 403);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'user', 'driver'])],
        ]);

        $user->update(['role' => $validated['role']]);

        return response()->json([
            'message' => 'Role updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'You cannot delete yourself.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
