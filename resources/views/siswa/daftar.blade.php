@extends('layouts.siswa')
@section('title', 'Pilih Ujian')
@section('content')
<div><h1 class="text-2xl font-extrabold tracking-tight">Ujian <span class="text-brand">tersedia</span></h1><p class="text-sm text-slate-500">Pilih ujian yang akan dikerjakan.</p></div>
@forelse ($banks as $b)
    @php $u = $uj[$b->id] ?? null; $warna = ['#6366f1,#8b5cf6', '#06b6d4,#3b82f6', '#f59e0b,#f97316', '#10b981,#14b8a6'][$loop->index % 4]; @endphp
    <div class="card card-hover flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" style="animation: fade-up .55s both; animation-delay: {{ $loop->index * 80 }}ms">
        <div class="flex items-center gap-4">
            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl text-lg font-extrabold text-white shadow-lg" style="background: linear-gradient(135deg,{{ $warna }})">{{ strtoupper(substr($b->mapel, 0, 1)) }}</div>
            <div><p class="font-bold">{{ $b->mapel }}</p>
                <p class="text-sm text-slate-500">{{ $b->jenis_ujian }} · {{ $b->soals_count }} soal · {{ $b->durasi }} menit</p></div>
        </div>
        @if($u?->status === 'selesai')<span class="badge bg-emerald-50 px-3 py-1 text-emerald-700">✓ Selesai</span>
        @else<a href="{{ route('siswa.konfirmasi', $b) }}" class="btn btn-primary">{{ $u ? 'Lanjutkan ujian' : 'Pilih ujian' }} →</a>@endif
    </div>
@empty
    <div class="card py-12 text-center text-slate-400">Belum ada ujian untuk kelas Anda.</div>
@endforelse
@endsection
