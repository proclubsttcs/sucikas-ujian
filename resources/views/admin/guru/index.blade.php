@extends('layouts.app')
@section('title', 'Guru & Pengawas')
@section('content')
<div class="card">
    <div class="mb-3 flex items-center justify-between"><h2 class="font-semibold">Daftar akun</h2><a href="{{ route('admin.guru.create') }}" class="btn btn-primary">Tambah guru</a></div>
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>Nama</th><th>NIP</th><th>Posisi</th><th></th></tr></thead>
            <tbody>
            @forelse ($guru as $g)
                <tr><td>{{ $g->name }}</td><td>{{ $g->nip_nis ?: '-' }}</td>
                    <td><span class="badge {{ $g->hasRole('guru') ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($g->getRoleNames()->first()) }}</span></td>
                    <td class="space-x-1 whitespace-nowrap text-right">
                        <a href="{{ route('admin.guru.edit', $g) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.guru.destroy', $g) }}" class="inline" onsubmit="return confirm('Hapus akun ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>
                    </td></tr>
            @empty<tr><td colspan="4" class="py-6 text-center text-slate-400">Belum ada akun.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
