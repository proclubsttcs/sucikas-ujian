<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    public function index()
    {
        $guru = User::role(['guru', 'pengawas'])->with('roles')->orderBy('name')->get();
        return view('admin.guru.index', compact('guru'));
    }

    public function create() { return view('admin.guru.form', ['u' => new User]); }

    public function store(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:120|unique:users,username',
            'posisi' => 'required|in:guru,pengawas',
            'nip_nis' => 'nullable|string|max:40',
            'password' => 'required|string|min:4',
        ]);
        $u = new User(['name' => $d['name'], 'username' => $d['name'], 'nip_nis' => $d['nip_nis'] ?? null]);
        $u->setPasswordBaru($d['password']);
        $u->save();
        $u->assignRole($d['posisi']);
        return redirect()->route('admin.guru.index')->with('ok', 'Akun berhasil ditambahkan.');
    }

    public function edit(User $guru) { return view('admin.guru.form', ['u' => $guru]); }

    public function update(Request $r, User $guru)
    {
        abort_if($guru->hasRole('admin') || $guru->hasRole('siswa'), 403);
        $d = $r->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('users', 'username')->ignore($guru->id)],
            'posisi' => 'required|in:guru,pengawas',
            'nip_nis' => 'nullable|string|max:40',
            'password' => 'nullable|string|min:4',
        ]);
        $guru->fill(['name' => $d['name'], 'username' => $d['name'], 'nip_nis' => $d['nip_nis'] ?? null]);
        if (!empty($d['password'])) $guru->setPasswordBaru($d['password']);
        $guru->save();
        $guru->syncRoles([$d['posisi']]);
        return redirect()->route('admin.guru.index')->with('ok', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $guru)
    {
        abort_if($guru->hasRole('admin') || $guru->hasRole('siswa') || $guru->id === auth()->id(), 403);
        $guru->delete();
        return back()->with('ok', 'Akun dihapus.');
    }
}
