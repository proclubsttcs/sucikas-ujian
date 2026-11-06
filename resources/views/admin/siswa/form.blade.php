@extends('layouts.app')
@section('title', $u->exists ? 'Edit Siswa' : 'Tambah Siswa')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $u->exists ? route('admin.siswa.update', $u) : route('admin.siswa.store') }}" class="card grid max-w-3xl gap-4 sm:grid-cols-2">
    @csrf @if($u->exists) @method('PUT') @endif
    <div class="sm:col-span-2"><label class="label">Nama lengkap</label><input name="name" value="{{ old('name', $u->name) }}" class="input" required></div>
    <div><label class="label">NIS (username login)</label><input name="nis" value="{{ old('nis', $u->nip_nis) }}" class="input" required></div>
    <div><label class="label">Kelas</label><input name="kelas" value="{{ old('kelas', $u->kelas) }}" class="input" required></div>
    <div><label class="label">Rombel</label><input name="rombel" value="{{ old('rombel', $u->rombel) }}" class="input"></div>
    <div><label class="label">Jurusan</label><input name="jurusan" value="{{ old('jurusan', $u->jurusan) }}" class="input" placeholder="UMUM"></div>
    <div><label class="label">Agama</label><input name="agama" value="{{ old('agama', $u->agama) }}" class="input"></div>
    <div><label class="label">Kata sandi {{ $u->exists ? '(kosongkan jika tidak diubah)' : '' }}</label><input name="password" class="input" {{ $u->exists ? '' : 'required' }}></div>
    <div class="sm:col-span-2"><label class="label">Foto siswa</label><input type="file" name="foto" accept="image/*" class="input"></div>
    <div class="flex gap-2 sm:col-span-2"><button class="btn btn-primary">{{ $u->exists ? 'Simpan' : 'Tambah' }}</button><a href="{{ route('admin.siswa.index') }}" class="btn btn-outline">Batal</a></div>
</form>
@endsection
