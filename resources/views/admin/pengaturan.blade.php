@extends('layouts.app')
@section('title', 'Pengaturan')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.pengaturan.update') }}" class="card grid max-w-4xl gap-4 sm:grid-cols-2">
    @csrf
    @foreach (['nama_sekolah' => 'Nama sekolah', 'kota' => 'Kota / kabupaten', 'alamat' => 'Alamat', 'kepala_sekolah' => 'Nama kepala sekolah', 'nip_kepsek' => 'NIP kepala sekolah', 'tahun_ajaran' => 'Tahun ajaran'] as $k => $l)
        <div class="{{ $k === 'alamat' ? 'sm:col-span-2' : '' }}"><label class="label">{{ $l }}</label><input name="{{ $k }}" value="{{ old($k, $s[$k]) }}" class="input"></div>
    @endforeach
    @foreach (['logo_sekolah' => 'Logo sekolah', 'logo_kota' => 'Logo kota', 'tanda_tangan' => 'Tanda tangan kepala sekolah'] as $k => $l)
        <div><label class="label">{{ $l }}</label>@if($urls[$k])<img src="{{ $urls[$k] }}" class="mb-2 h-14 rounded border border-slate-200 bg-white p-1">@endif<input type="file" name="{{ $k }}" accept="image/*" class="input"></div>
    @endforeach
    <div class="sm:col-span-2"><button class="btn btn-primary">Terapkan</button></div>
</form>
@endsection
