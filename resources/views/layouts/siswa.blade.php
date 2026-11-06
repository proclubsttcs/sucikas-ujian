<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ujian') — Sucikas Ujian</title>
    <link rel="preconnect" href="https://fonts.bunny.net"><link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans text-slate-900 antialiased">
<header class="relative overflow-hidden" style="background:linear-gradient(120deg,#312e81,#4f46e5,#7c3aed);background-size:200% 200%;animation:gradient-pan 12s ease infinite">
    <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/15 blur-2xl" style="animation:float 8s ease-in-out infinite"></div>
    <div class="relative mx-auto flex h-16 max-w-3xl items-center gap-3 px-4">
        <div class="grid h-10 w-10 place-items-center rounded-xl bg-white/20 font-extrabold text-white ring-1 ring-white/30 backdrop-blur">S</div>
        <span class="truncate font-bold text-white">{{ \App\Models\Setting::get('nama_sekolah', 'Sucikas Ujian') }}</span>
        <div class="ml-auto flex items-center gap-2">
            <span class="hidden text-sm text-indigo-100 sm:inline">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm border border-white/40 bg-white/15 text-white backdrop-blur hover:bg-white/25">Keluar</button></form>
        </div>
    </div>
</header>
<main class="mx-auto max-w-3xl space-y-4 p-4 sm:py-8">@include('layouts.flash')@yield('content')</main>
@stack('scripts')
</body>
</html>
