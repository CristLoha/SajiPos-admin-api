<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user registration (kasir).
     * POST /api/register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'roles' => null, // role ditentukan admin saat approve
            'status_akun' => 'pending_approval',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil, menunggu persetujuan admin.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status_akun' => $user->status_akun,
            ]
        ], 201);
    }

    /**
     * Handle user login and issue API token.
     * POST /api/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required_without:username|string',
            'username' => 'required_without:email|string',
            'password' => 'required|string',
        ]);

        // Get the login identifier (either email or username provided in request)
        $loginIdentifier = $request->input('email') ?: $request->input('username');

        // Support login by email or username
        $loginField = filter_var($loginIdentifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginField, $loginIdentifier)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.'
            ], 401);
        }

        if ($user->status_akun === 'pending_approval') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda sedang menunggu persetujuan Admin.'
            ], 403);
        }

        if ($user->status_akun === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran Anda ditolak.',
                'alasan' => $user->rejection_reason
            ], 403);
        }

        if ($user->status_akun === 'nonaktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah dinonaktifkan.'
            ], 403);
        }

        // Generate Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->roles,
            ]
        ], 200);
    }

    /**
     * Handle user logout and revoke API token.
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil!'
        ], 200);
    }
}
