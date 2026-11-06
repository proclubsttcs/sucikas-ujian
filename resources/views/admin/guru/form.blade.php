@extends('layouts.app')
@section('title', $u->exists ? 'Edit Guru / Pengawas' : 'Tambah Guru / Pengawas')
@section('content')
<form method="POST" action="{{ $u->exists ? route('admin.guru.update', $u) : route('admin.guru.store') }}" class="card max-w-xl space-y-4">
    @csrf @if($u->exists) @method('PUT') @endif
    <div><label class="label">Nama lengkap (dipakai sebagai username)</label><input name="name" value="{{ old('name', $u->name) }}" class="input" required></div>
    <div><label class="label">Posisi</label>
        <select name="posisi" class="input">@foreach (['guru' => 'Guru', 'pengawas' => 'Pengawas'] as $k => $v)<option value="{{ $k }}" @selected(old('posisi', $u->getRoleNames()->first()) === $k)>{{ $v }}</option>@endforeach</select></div>
    <div><label class="label">NIP</label><input name="nip_nis" value="{{ old('nip_nis', $u->nip_nis) }}" class="input"></div>
    <div><label class="label">Kata sandi {{ $u->exists ? '(kosongkan jika tidak diubah)' : '' }}</label><input type="text" name="password" class="input" {{ $u->exists ? '' : 'required' }}></div>
    <div class="flex gap-2"><button class="btn btn-primary">{{ $u->exists ? 'Simpan' : 'Tambah' }}</button><a href="{{ route('admin.guru.index') }}" class="btn btn-outline">Batal</a></div>
</form>
@endsection
