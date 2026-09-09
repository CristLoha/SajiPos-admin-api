<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get list of users (can filter by status)
     * GET /api/users?status=pending_approval
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('status')) {
            $query->where('status_akun', $request->status);
        }

        $users = $query->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Approve a user
     * POST /api/users/{id}/approve
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,staff,user' // Using role enum instead of role_id
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'status_akun' => 'approved',
            'roles'       => $request->role,
            'approved_by' => $request->user()?->id ?? 1, // Fallback if no auth context
            'approved_at' => now(),
        ]);

        \Illuminate\Support\Facades\Cache::forget('polling_pending_users');

        return response()->json([
            'success' => true,
            'message' => 'User berhasil disetujui.',
            'data' => $user
        ]);
    }

    /**
     * Reject a user
     * POST /api/users/{id}/reject
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'status_akun'      => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        \Illuminate\Support\Facades\Cache::forget('polling_pending_users');

        return response()->json([
            'success' => true,
            'message' => 'User ditolak.',
            'data' => $user
        ]);
    }
}
