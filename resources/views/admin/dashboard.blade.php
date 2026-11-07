@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

@role('admin')
<div class="relative overflow-hidden rounded-3xl bg-[#1e1b4b] p-6 shadow-2xl sm:p-8">
    <!-- Latar Belakang Gambar (Gunakan gambar yang sama dengan sidebar) -->
    <img src="{{ asset('images/2.jpg') }}" alt="Background Token" class="absolute inset-0 z-0 h-full w-full object-cover opacity-50 blur-[2px]">
    
    <!-- Overlay Gradasi Gelap -->
    <div class="absolute inset-0 z-0 bg-gradient-to-r from-indigo-950/90 to-violet-900/80"></div>
    
    <!-- Konten Token (Relative z-10) -->
    <div class="relative z-10 flex flex-col items-center justify-between gap-6 md:flex-row">
        <div class="text-center md:text-left">
            <p class="mb-2 text-sm font-medium text-indigo-200">Token ujian saat ini</p>
            
            <!-- Variabel Token Tetap Aman -->
            <h2 class="mb-2 font-mono text-5xl font-extrabold tracking-[0.35em] text-white drop-shadow-md sm:text-6xl">
                {{ $token }}
            </h2>
            
            <p class="text-sm text-indigo-200/70">Bagikan kepada siswa saat ujian dimulai.</p>
        </div>
        
        <!-- Form Ganti Token Tetap Aman -->
        <form method="POST" action="{{ route('admin.token') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-6 py-3 text-sm font-bold text-white shadow-lg backdrop-blur-md transition-all hover:scale-105 hover:bg-white/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Ganti token
            </button>
        </form>
    </div>
</div>
@endrole

<!-- Bagian Statistik (Tidak Dirubah) -->
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

<!-- Bagian Tabel Ujian (Tidak Dirubah) -->
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