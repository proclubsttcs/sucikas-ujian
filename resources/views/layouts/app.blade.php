@php
    $u = auth()->user();
    $ic = [
        'home' => 'M3 12l9-9 9 9M5 10v10h14V10',
        'users' => 'M16 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M9.5 10a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM21 20v-2a4 4 0 00-3-3.87M16 3.13a3.5 3.5 0 010 6.74',
        'book' => 'M4 5a2 2 0 012-2h13v16H6a2 2 0 00-2 2V5zM4 19a2 2 0 002 2h13',
        'file' => 'M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5zM14 3v5h5M9 13h6M9 17h6',
        'monitor' => 'M3 4h18v12H3zM8 20h8M12 16v4',
        'chart' => 'M4 20V10m6 10V4m6 16v-7m4 7H2',
        'cog' => 'M4 6h16M4 12h16M4 18h16M8 4v4M16 10v4M10 16v4',
        'db' => 'M4 6c0-1.7 3.6-3 8-3s8 1.3 8 3-3.6 3-8 3-8-1.3-8-3zM4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3',
    ];
    $menu = [];
    if ($u->hasAnyRole(['admin', 'guru'])) $menu[] = ['Dashboard', 'admin.dashboard', 'home', 'admin.dashboard'];
    if ($u->hasRole('admin')) { $menu[] = ['Guru & Pengawas', 'admin.guru.index', 'users', 'admin.guru.*']; $menu[] = ['Siswa', 'admin.siswa.index', 'users', 'admin.siswa.*']; }
    if ($u->hasAnyRole(['admin', 'guru'])) {
        $menu[] = ['Bank Soal', 'admin.bank.index', 'book', 'admin.bank.*'];
        $menu[] = ['Kartu, Hadir, Berita Acara', 'admin.dokumen', 'file', 'admin.dokumen*'];
        $menu[] = ['Hasil Ujian', 'admin.hasil', 'chart', 'admin.hasil*'];
    }
    if ($u->hasAnyRole(['admin', 'pengawas'])) $menu[] = ['Monitor Ujian', 'admin.monitor', 'monitor', 'admin.monitor*'];
    if ($u->hasRole('admin')) { $menu[] = ['Pengaturan', 'admin.pengaturan', 'cog', 'admin.pengaturan*']; $menu[] = ['Update & Backup', 'admin.sistem', 'db', 'admin.sistem*']; }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ \App\Models\Setting::get('nama_sekolah', 'Sucikas Ujian') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net"><link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-900 antialiased">
<div class="min-h-screen lg:flex">
    <div id="overlay" class="fixed inset-0 z-30 hidden bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>
    <aside id="sidebar" class="no-print fixed inset-y-0 left-0 z-40 flex w-68 -translate-x-full flex-col overflow-hidden transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0" style="width:17rem;background:linear-gradient(170deg,#1e1b4b 0%,#312e81 55%,#4c1d95 100%)">
        <div class="pointer-events-none absolute -left-16 -top-16 h-56 w-56 rounded-full bg-violet-500/30 blur-3xl" style="animation: float 9s ease-in-out infinite"></div>
        <div class="pointer-events-none absolute -bottom-20 -right-10 h-56 w-56 rounded-full bg-cyan-400/20 blur-3xl" style="animation: drift 11s ease-in-out infinite"></div>
        <div class="relative flex h-16 items-center gap-3 px-5">
            <div class="bg-brand grid h-10 w-10 place-items-center rounded-xl text-lg font-extrabold text-white shadow-lg shadow-black/30" style="animation: pulse-ring 2.4s ease-out infinite">S</div>
            <div class="leading-tight"><p class="font-bold text-white">Sucikas Ujian</p><p class="text-[11px] text-indigo-200/70">Computer Based Test</p></div>
        </div>
        <nav class="relative flex-1 space-y-1 overflow-y-auto px-3 py-3">
            @foreach ($menu as [$label, $route, $icon, $pattern])
                <a href="{{ route($route) }}" class="nav-link {{ request()->routeIs($pattern) ? 'nav-link-active' : '' }}" style="animation: slide-in .5s ease both; animation-delay: {{ $loop->index * 50 }}ms">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $ic[$icon] }}"/></svg>
                    <span class="truncate">{{ $label }}</span>
                </a>
            @endforeach
        </nav>
        <div class="relative border-t border-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="grid h-9 w-9 place-items-center rounded-full bg-white/15 text-sm font-bold text-white">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                <div class="min-w-0"><p class="truncate text-sm font-medium text-white">{{ $u->name }}</p><p class="text-xs text-indigo-200/70">{{ ucfirst($u->getRoleNames()->first()) }}</p></div>
            </div>
        </div>
    </aside>
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="no-print sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-white/60 bg-white/70 px-4 backdrop-blur-xl sm:px-6">
            <button id="burger" class="rounded-xl p-2 text-slate-600 transition hover:bg-indigo-50 hover:text-primary lg:hidden" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M4 7h16M4 12h10M4 17h16"/></svg>
            </button>
            <h1 class="truncate text-lg font-bold tracking-tight">@yield('title', 'Dashboard')</h1>
            <div class="ml-auto flex items-center gap-3">
                <span class="hidden items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 sm:inline-flex"><span class="h-2 w-2 rounded-full bg-emerald-500" style="animation: pulse-ring 1.8s ease-out infinite"></span>Online</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline btn-sm">Keluar</button></form>
            </div>
        </header>
        <main class="flex-1 space-y-5 p-4 sm:p-6 lg:p-8">
            @include('layouts.flash')
            @yield('content')
        </main>
    </div>
</div>
<script>
    const sb = document.getElementById('sidebar'), ov = document.getElementById('overlay');
    const tog = () => { sb.classList.toggle('-translate-x-full'); ov.classList.toggle('hidden'); };
    document.getElementById('burger').onclick = tog; ov.onclick = tog;
    // count-up angka statistik
    document.querySelectorAll('[data-count]').forEach(el => {
        const to = parseFloat(el.dataset.count) || 0, t0 = performance.now();
        const step = t => { const p = Math.min(1, (t - t0) / 900); el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3))); if (p < 1) requestAnimationFrame(step); };
        requestAnimationFrame(step);
    });
</script>
@stack('scripts')
</body>
</html>
