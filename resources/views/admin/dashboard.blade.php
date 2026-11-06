@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
@role('admin')
<div class="bg-brand relative overflow-hidden rounded-3xl p-6 text-white shadow-xl shadow-indigo-500/25 sm:p-8">
    <div class="pointer-events-none absolute -right-10 -top-16 h-56 w-56 rounded-full bg-white/15 blur-2xl" style="animation:float 9s ease-in-out infinite"></div>
    <div class="pointer-events-none absolute -bottom-20 left-1/3 h-48 w-48 rounded-full bg-cyan-300/25 blur-2xl" style="animation:drift 11s ease-in-out infinite"></div>
    <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-indigo-100">Token ujian saat ini</p>
            <p class="mt-1 font-mono text-4xl font-extrabold tracking-[0.35em] sm:text-5xl" style="text-shadow:0 4px 24px rgba(0,0,0,.25)">{{ $token }}</p>
            <p class="mt-2 text-xs text-indigo-100/80">Bagikan kepada siswa saat ujian dimulai.</p>
        </div>
        <form method="POST" action="{{ route('admin.token') }}">@csrf<button class="btn border border-white/40 bg-white/15 text-white backdrop-blur hover:bg-white/25">⟳ Ganti token</button></form>
    </div>
</div>
@endrole
<div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
    @foreach ([
        ['Siswa mengikuti ujian', $jmlSiswaUjian, 'linear-gradient(135deg,#6366f1,#8b5cf6)', 'rgba(99,102,241,.14)', 'M16 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M9.5 10a3.5 3.5 0 100-7 3.5 3.5 0 000 7z'],
        ['Total siswa', $jmlSiswa, 'linear-gradient(135deg,#06b6d4,#3b82f6)', 'rgba(6,182,212,.14)', 'M12 14l9-5-9-5-9 5 9 5zM5 12v5c0 1 3 3 7 3s7-2 7-3v-5'],
        ['Bank soal', $jmlBank, 'linear-gradient(135deg,#f59e0b,#f97316)', 'rgba(245,158,11,.16)', 'M4 5a2 2 0 012-2h13v16H6a2 2 0 00-2 2V5zM4 19a2 2 0 002 2h13'],
        ['Soal aktif', $jmlSoalAktif, 'linear-gradient(135deg,#10b981,#14b8a6)', 'rgba(16,185,129,.16)', 'M5 13l4 4L19 7'],
    ] as [$l, $v, $g, $tint, $path])
        <div class="stat" style="--tint: {{ $tint }}">
            <div class="stat-icon" style="background: {{ $g }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $path }}"/></svg></div>
            <p class="text-3xl font-extrabold tracking-tight" data-count="{{ $v }}">0</p>
            <p class="mt-0.5 text-xs font-medium text-slate-500">{{ $l }}</p>
        </div>
    @endforeach
</div>
<div class="card">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="flex items-center gap-2 text-base font-bold"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500" style="animation:pulse-ring 1.8s ease-out infinite"></span>Ujian sedang berlangsung</h2>
        <form class="flex gap-2"><input name="q" value="{{ request('q') }}" placeholder="Cari mapel / kode…" class="input sm:w-64"><button class="btn btn-primary">Cari</button></form>
    </div>
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>Kode</th><th>Mapel</th><th>Jenis</th><th>Kelas</th><th>Durasi</th><th>Soal</th></tr></thead>
            <tbody>
            @forelse ($banks as $b)
                <tr><td class="font-mono text-xs">{{ $b->kode }}</td><td class="font-semibold">{{ $b->mapel }}</td><td><span class="badge bg-indigo-50 text-indigo-700">{{ $b->jenis_ujian }}</span></td>
                    <td>{{ $b->kelas }} {{ $b->rombel }} {{ $b->jurusan }}</td><td>{{ $b->durasi }} mnt</td><td>{{ $b->soals_count }}</td></tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-slate-400">Belum ada ujian aktif.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
