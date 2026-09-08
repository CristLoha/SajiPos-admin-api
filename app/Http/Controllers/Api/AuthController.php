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
            'username' => 'required|string|max:50', // unique dicek manual
            'email' => 'required|email:rfc,dns', // unique dicek manual
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/', // harus ada huruf kecil
                'regex:/[A-Z]/', // harus ada huruf besar
                'regex:/[0-9]/', // harus ada angka
                'confirmed'
            ],
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.min' => 'Password minimal 8 karakter.',
            'email.email' => 'Format email tidak valid atau domain tidak ditemukan.',
        ]);

        // Cek apakah email sudah ada
        $existingEmail = User::where('email', $validated['email'])->first();
        if ($existingEmail && $existingEmail->email_verified_at !== null) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors' => ['email' => ['Email ini sudah terdaftar dan terverifikasi.']]
            ], 422);
        }

        // Cek apakah username sudah dipakai orang lain
        $existingUsername = User::where('username', $validated['username'])->first();
        if ($existingUsername && (!$existingEmail || $existingUsername->id !== $existingEmail->id)) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors' => ['username' => ['Username ini sudah digunakan. Silakan pilih username lain.']]
            ], 422);
        }

        $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            $user = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $otpCode, $existingEmail) {
                if ($existingEmail) {
                    // JALAN NINJA: Timpa data user lama yang belum diverifikasi
                    $existingEmail->update([
                        'name' => $validated['nama_lengkap'],
                        'username' => $validated['username'],
                        'password' => Hash::make($validated['password']),
                        'status_akun' => 'pending_approval', // WAJIB DIRESET ke pending
                        'otp_code' => $otpCode,
                        'otp_expires_at' => now()->addMinutes(10),
                    ]);
                    $user = $existingEmail;
                } else {
                    // Buat user baru
                    $user = User::create([
                        'name' => $validated['nama_lengkap'],
                        'email' => $validated['email'],
                        'username' => $validated['username'],
                        'password' => Hash::make($validated['password']),
                        'roles' => 'user', // default role adalah kasir
                        'status_akun' => 'pending_approval',
                        'otp_code' => $otpCode,
                        'otp_expires_at' => now()->addMinutes(10),
                    ]);
                }

                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($user, $otpCode));

                return $user;
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal kirim email OTP (Transaction Rollback): ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Email tidak valid, tidak terdaftar, atau server gagal mengirim pesan.',
                'errors' => [
                    'email' => ['Alamat email ini tidak dapat menerima pesan. Pastikan email benar-benar aktif.']
                ]
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil. Silakan cek email Anda untuk kode OTP verifikasi.',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ]
        ], 201);
    }

    /**
     * Real-time email validation for Frontend (Flutter).
     * POST /api/check-email
     */
    public function checkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email:rfc,dns'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'is_valid' => false,
                'message' => 'Format email tidak valid atau domain tidak ditemukan.'
            ], 200);
        }

        $existingUser = User::where('email', $request->email)->first();
        
        if ($existingUser && $existingUser->email_verified_at !== null) {
            return response()->json([
                'is_valid' => false,
                'message' => 'Email sudah terdaftar dan terverifikasi.'
            ], 200);
        }

        return response()->json([
            'is_valid' => true,
            'message' => 'Email bisa digunakan.'
        ], 200);
    }
    /**
     * Verify Email with OTP
     * POST /api/verify-email
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Email sudah diverifikasi.'], 400);
        }

        if ($user->otp_code !== $request->otp_code) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah.'], 400);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'Kode OTP sudah kadaluarsa.'], 400);
        }

        // Verifikasi berhasil
        $user->update([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi! Menunggu persetujuan admin sebelum bisa login.'
        ], 200);
    }

    /**
     * Resend OTP
     * POST /api/resend-otp
     */
    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Email sudah diverifikasi.'], 400);
        }

        $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($user, $otpCode));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal resend email OTP: ' . $e->getMessage());
            return response()->json([
                'message' => 'Email gagal dikirim.',
                'errors' => [
                    'email' => ['Alamat email ini tidak dapat menerima pesan. Pastikan email benar-benar aktif.']
                ]
            ], 422);
        }

        return response()->json(['success' => true, 'message' => 'Kode OTP baru telah dikirim ke email.'], 200);
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

    /**
     * Generate username & secure password suggestions
     * GET /api/suggestions/credentials
     */
    public function generateSuggestions(Request $request)
    {
        $namaLengkap = $request->input('nama_lengkap', 'Kasir');

        // Generate Username (format: namakacil + 3 angka acak)
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $namaLengkap));
        if (strlen($baseUsername) < 3) $baseUsername = "user";
        
        $username = $baseUsername . rand(100, 999);
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . rand(100, 999);
        }

        // Generate Password (Min 8 char, Huruf Besar, Huruf Kecil, Angka)
        // Format: NamaCapital + "Pos" + 3 angka
        $baseName = ucfirst(preg_replace('/[^a-zA-Z]/', '', $namaLengkap));
        if (strlen($baseName) < 3) $baseName = "Saji";
        $baseName = substr($baseName, 0, 5); // Biar nggak kepanjangan
        
        $password = ucfirst($baseName) . 'Pos' . rand(100, 999);

        return response()->json([
            'success' => true,
            'message' => 'Saran username dan password berhasil dibuat.',
            'data' => [
                'username' => $username,
                'password' => $password
            ]
        ], 200);
    }
}
