<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get list of cashiers (staff) by status
     * GET /api/users/cashiers?status=pending
     */
    public function getCashiers(Request $request)
    {
        $query = User::where('roles', 'staff');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $cashiers = $query->get();

        return response()->json([
            'success' => true,
            'data' => $cashiers
        ]);
    }

    /**
     * Approve or reject a cashier
     * POST /api/users/{id}/confirm
     */
    public function confirmCashier(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,rejected'
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        if ($user->roles !== 'staff') {
            return response()->json([
                'success' => false,
                'message' => 'User ini bukan kasir/staff'
            ], 400);
        }

        $user->status = $request->status;
        $user->save();

        $message = $request->status === 'active' ? 'Akun kasir berhasil disetujui.' : 'Akun kasir ditolak.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $user
        ]);
    }
}
