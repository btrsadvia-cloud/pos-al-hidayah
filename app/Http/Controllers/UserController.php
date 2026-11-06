<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%");
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $users = $query->paginate(15)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'level'    => ['required', 'in:1,2'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'username'  => $data['username'],
            'password'  => Hash::make($data['password']),
            'level'     => (int) $data['level'],
            'is_active' => true,
        ]);

        AuditLog::record(
            action: 'Tambah Pengguna Baru',
            subject: $user,
            newValues: ['name' => $user->name, 'username' => $user->username, 'level' => $user->level]
        );

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'level'    => ['required', 'in:1,2'],
        ]);

        $updateData = [
            'name'     => $data['name'],
            'username' => $data['username'],
            'level'    => (int) $data['level'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $oldValues = ['name' => $user->name, 'username' => $user->username, 'level' => $user->level];
        $user->update($updateData);

        AuditLog::record(
            action: 'Ubah Data Pengguna',
            subject: $user,
            oldValues: $oldValues,
            newValues: $updateData
        );

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::record(
            action: "Status Pengguna {$status}",
            subject: $user,
            newValues: ['is_active' => $user->is_active]
        );

        return redirect()->route('users.index')->with('success', "Akun {$user->name} berhasil {$status}.");
    }
}

