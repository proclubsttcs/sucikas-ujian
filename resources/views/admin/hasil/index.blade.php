@extends('layouts.app')
@section('title', 'Hasil Ujian')
@section('content')
<div class="card flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
    <p class="text-sm">Siswa boleh melihat nilai setelah ujian: <b>{{ $tampil ? 'Ya' : 'Tidak' }}</b></p>
    <form method="POST" action="{{ route('admin.hasil.tampil') }}">@csrf<button class="btn btn-outline btn-sm">{{ $tampil ? 'Sembunyikan nilai dari siswa' : 'Tampilkan nilai ke siswa' }}</button></form>
</div>
<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($banks as $b)
        <a href="{{ route('admin.hasil.bank', $b) }}" class="card block hover:border-secondary">
            <p class="font-semibold">{{ $b->mapel }}</p><p class="text-xs text-slate-500">{{ $b->jenis_ujian }} · Kelas {{ $b->kelas }} {{ $b->rombel }} · {{ $b->kode }}</p>
            <p class="mt-2 text-sm text-primary">{{ $b->selesai_count }} siswa selesai</p></a>
    @empty<p class="text-slate-400">Belum ada bank soal yang dikerjakan.</p>@endforelse
</div>
@endsection
