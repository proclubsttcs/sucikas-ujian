@extends('layouts.app')
@section('title', 'Update & Backup')
@section('content')
<div class="grid gap-4 md:grid-cols-2">
    <div class="card space-y-3"><h2 class="font-semibold">Update aplikasi</h2>
        <p class="text-sm text-slate-600">Setelah berkas aplikasi versi terbaru diunggah ke server, jalankan pembaruan untuk memigrasi database dan membersihkan cache.</p>
        <p class="text-xs text-slate-500">{{ $versi }}</p>
        <form method="POST" action="{{ route('admin.sistem.update') }}" onsubmit="return confirm('Jalankan pembaruan sistem?')">@csrf<button class="btn btn-primary">Perbarui sistem</button></form></div>
    <div class="card space-y-3"><h2 class="font-semibold">Backup database</h2>
        <p class="text-sm text-slate-600">Unduh cadangan seluruh database (.sql). Simpan di tempat yang aman.</p>
        <a href="{{ route('admin.sistem.backup') }}" class="btn btn-outline">Unduh backup</a></div>
</div>
@endsection
