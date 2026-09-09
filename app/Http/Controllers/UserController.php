<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Cek apakah user adalah admin (untuk aksi CRUD)
    private function isAdmin()
    {
        if (auth()->user()->roles !== 'admin') {
            abort(403, 'Hanya admin yang bisa melakukan aksi ini.');
        }
    }
    // polling status email (lightweight)
    public function polling()
    {
        // Gunakan cache selama 5 detik biar kalau ada 100 tab admin yg kebuka,
        // database MySQL cuma dipanggil 1x per 5 detik. Sisanya diambil cepat dari RAM.
        // Ini jalan ninja buat nyelamatin CPU dan Database di shared hosting!
        $users = \Illuminate\Support\Facades\Cache::remember('polling_pending_users', 5, function () {
            return User::where('status_akun', 'pending_approval')
                ->whereNotNull('email_verified_at')
                ->get(['id', 'email_verified_at']);
        });
            
        return response()->json(['users' => $users]);
    }

    // index
    public function index(Request $request)
    {
        // Hanya admin dan staff yang boleh melihat daftar semua user
        if (auth()->user()->roles !== 'admin' && auth()->user()->roles !== 'staff') {
            abort(403, 'Unauthorized. Hanya admin dan staff yang bisa melihat daftar user.');
        }

        //get all users with search, filter, and pagination
        $users = User::when($request->name, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->name . '%');
        })
        ->when($request->status, function ($query) use ($request) {
            $query->where('status_akun', $request->status);
        })
        ->orderByRaw("FIELD(status_akun, 'pending_approval') DESC") // Prioritize pending
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('pages.users.index', compact('users'));
    }

    // create
    public function create()
    {
        $this->isAdmin();
        return view('pages.users.create');
    }

    // store
    public function store(Request $request)
    {
        $this->isAdmin();
        // validate
        $request->validate([
            'name' => 'required',
            'username' => 'required|alpha_dash|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'roles' => 'required|in:admin,staff,user',
        ]);

        // create user
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'roles' => $request->roles,
        ]);

        // redirect
        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    // show
    public function show($id)
    {
        return view('pages.users.show');
    }

    // edit
    public function edit($id)
    {
        // Staff/user hanya boleh mengedit profil dirinya sendiri. Admin boleh mengedit siapa saja.
        if (auth()->user()->roles !== 'admin' && auth()->id() != $id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit akun ini.');
        }

        $user = User::findOrFail($id);
        return view('pages.users.edit', compact('user'));
    }

    // update
    public function update(Request $request, $id)
    {
        // Staff/user hanya boleh mengedit profil dirinya sendiri. Admin boleh mengedit siapa saja.
        if (auth()->user()->roles !== 'admin' && auth()->id() != $id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit akun ini.');
        }

        // Validasi dasar
        $rules = [
            'name' => 'required',
            'username' => 'required|alpha_dash|max:255|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
        ];

        // Hanya admin yang wajib mengirimkan input role dan bisa mengubahnya
        if (auth()->user()->roles === 'admin') {
            $rules['roles'] = 'required|in:admin,staff,user';
        }

        $request->validate($rules);

        // update user
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        // Hanya ubah role jika user yang sedang login adalah admin
        if (auth()->user()->roles === 'admin') {
            $user->roles = $request->roles;
        }

        $user->save();

        // redirect ke home jika staff yang mengedit profilnya sendiri
        if (auth()->user()->roles !== 'admin') {
            return redirect()->route('home')->with('success', 'Profil Anda berhasil diperbarui!');
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    // destroy
    public function destroy($id)
    {
        $this->isAdmin();

        // Cegah admin hapus dirinya sendiri
        if (auth()->user()->id == $id) {
            return redirect()->route('users.index')->with('error', 'Gak bisa hapus akun sendiri bro!');
        }

        // delete user
        $user = User::findOrFail($id);
        $user->delete();

        // redirect
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    // approve
    public function approve(Request $request, $id)
    {
        $this->isAdmin();
        
        $request->validate([
            'role' => 'required|in:admin,staff,user'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'status_akun' => 'approved',
            'roles'       => $request->role,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        \Illuminate\Support\Facades\Cache::forget('polling_pending_users');

        return redirect()->back()->with('success', 'User berhasil disetujui.');
    }

    // reject
    public function reject(Request $request, $id)
    {
        $this->isAdmin();

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'status_akun'      => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        \Illuminate\Support\Facades\Cache::forget('polling_pending_users');

        return redirect()->back()->with('success', 'User ditolak.');
    }

    // edit profile (khusus untuk user login saat ini)
    public function editProfile()
    {
        $user = auth()->user();
        return view('pages.profile.edit', compact('user'));
    }

    // update profile (khusus untuk user login saat ini)
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required',
            'username' => 'required|alpha_dash|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('home')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    public function resetDevice($id)
    {
        $this->isAdmin();
        $user = User::findOrFail($id);
        $user->update(['device_id' => null]);
        return redirect()->back()->with('success', 'Device ID berhasil direset. User dapat login di perangkat baru.');
    }
}
