<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Sucikas Ujian</title>
    <link rel="preconnect" href="https://fonts.bunny.net"><link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen overflow-hidden font-sans antialiased" style="background:linear-gradient(120deg,#312e81,#4f46e5,#7c3aed,#0ea5e9);background-size:300% 300%;animation:gradient-pan 14s ease infinite">
<div class="pointer-events-none absolute -left-24 top-10 h-80 w-80 rounded-full bg-fuchsia-400/30 blur-3xl" style="animation:float 10s ease-in-out infinite"></div>
<div class="pointer-events-none absolute -right-20 bottom-0 h-96 w-96 rounded-full bg-cyan-300/30 blur-3xl" style="animation:drift 12s ease-in-out infinite"></div>
<div class="pointer-events-none absolute left-1/2 top-1/3 h-64 w-64 rounded-full bg-indigo-300/20 blur-3xl" style="animation:float 14s ease-in-out infinite"></div>

<div class="relative grid min-h-screen place-items-center p-4">
    <div class="w-full max-w-md">
        <div class="fade-up mb-7 text-center text-white">
            @if ($logo = \App\Models\Setting::url('logo_sekolah'))<img src="{{ $logo }}" class="pop mx-auto mb-4 h-16 drop-shadow-xl" alt="Logo">
            @else<div class="pop mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-white/20 text-3xl font-extrabold shadow-2xl ring-1 ring-white/40 backdrop-blur" style="animation:pop .5s both,pulse-ring 2.6s 1s ease-out infinite">S</div>@endif
            <h1 class="text-3xl font-extrabold tracking-tight">Sucikas Ujian</h1>
            <p class="mt-1 text-sm text-indigo-100/90">{{ \App\Models\Setting::get('nama_sekolah', 'Ujian Berbasis Komputer') }}</p>
        </div>
        <form method="POST" action="{{ url('/login') }}" class="fade-up space-y-5 rounded-3xl border border-white/30 bg-white/90 p-7 shadow-2xl shadow-indigo-950/40 backdrop-blur-xl" style="animation-delay:.12s">
            @csrf
            <div>
                <h2 class="text-lg font-bold text-slate-900">Selamat datang 👋</h2>
                <p class="text-sm text-slate-500">Masuk untuk melanjutkan ke ujian.</p>
            </div>
            <div>
                <label class="label" for="username">Username / NIS</label>
                <input id="username" name="username" value="{{ old('username') }}" class="input" autocomplete="username" autofocus required>
                @error('username')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="password">Kata sandi</label>
                <div class="relative">
                    <input id="password" type="password" name="password" class="input pr-16" autocomplete="current-password" required>
                    <button type="button" id="lihat" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg px-2 py-1 text-xs font-semibold text-primary hover:bg-indigo-50">Lihat</button>
                </div>
            </div>
            <button class="btn btn-primary w-full py-3 text-base">Masuk</button>
        </form>
        <p class="mt-5 text-center text-xs text-indigo-100/80">© {{ date('Y') }} Sucikas Ujian</p>
    </div>
</div>
<script>
    const p = document.getElementById('password'), b = document.getElementById('lihat');
    b.onclick = () => { const s = p.type === 'password'; p.type = s ? 'text' : 'password'; b.textContent = s ? 'Sembunyi' : 'Lihat'; };
</script>
</body>
</html>
