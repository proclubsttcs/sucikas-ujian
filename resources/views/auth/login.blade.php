<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CBT Faturweb</title>
    
    <!-- Tambahkan baris ini untuk mengubah logo di tab browser -->
    <link rel="icon" type="image/png" href="{{ asset('images/faturweb.png') }}">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen flex items-center justify-center font-sans antialiased overflow-hidden">
    
    <!-- Latar Belakang Gambar dengan Efek Buram (Blur) -->
    <div class="absolute inset-0 z-0 overflow-hidden">
        <!-- scale-105 digunakan agar efek blur tidak memunculkan garis putih di tepi layar -->
        <img src="{{ asset('images/2.jpg') }}" alt="Background" class="absolute inset-0 w-full h-full object-cover blur-sm scale-105">
        
        <!-- Overlay gelap transparan agar kartu login putih tetap menonjol -->
        <div class="absolute inset-0 bg-slate-900/40"></div>
    </div>

    <!-- Main Card Container (Kartu Login yang sudah ada) -->
    <div class="relative z-10 w-full max-w-5xl p-4">
        <div class="flex flex-col md:flex-row bg-white rounded-[2.5rem] shadow-[0_20px_50px_-12px_rgba(0,0,0,0.08)] p-2.5">
            <!-- Panel Kiri (Full Gambar Poster Developer) -->
            <div class="hidden md:block w-1/2 rounded-[2rem] relative overflow-hidden bg-white">
                <!-- Pastikan gambar hasil editan/AI Anda disimpan di folder public/images/ dengan nama yang sesuai -->
                <img src="{{ asset('images/Poster Aplikasi Ujian CBT Modern.png') }}" alt="Developer Sucikas Ujian" class="absolute inset-0 w-full h-full object-cover" onerror="this.outerHTML='<div class=\'absolute inset-0 bg-slate-100 flex items-center justify-center text-slate-400 text-sm\'>[Gambar Poster Developer]</div>'">
            </div>

            <!-- Panel Kanan (Form Login) -->
            <div class="flex flex-col justify-center w-full md:w-1/2 p-8 md:p-16 relative z-10">
                <div class="text-center mb-10">
                    <!-- Logo Header -->
                    <div class="flex items-center justify-center gap-2 mb-6">
                        @if ($logo = \App\Models\Setting::url('logo_sekolah'))
                            <img src="{{ $logo }}" class="h-8" alt="Logo">
                        @else
                            <div class="grid h-8 w-8 place-items-center rounded-full bg-[#1E40AF] text-white text-sm font-bold shadow-md">S</div>
                        @endif
                        <span class="text-xl font-bold text-slate-800 tracking-tight">CBT FATURWEB</span>
                    </div>

                    <!-- Teks dipertahankan menggunakan Bahasa Indonesia sesuai permintaan sebelumnya -->
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Selamat Datang</h2>
                    <p class="mt-2 text-sm text-slate-400 font-medium">Silakan masuk untuk memulai ujian</p>
                </div>

                <form method="POST" action="{{ url('/login') }}" class="space-y-4">
                    @csrf
                    
                    <!-- Input Username -->
                    <div>
                        <input id="username" name="username" value="{{ old('username') }}" placeholder="Username / NIS" class="w-full bg-slate-50 border border-transparent rounded-xl px-4 py-4 text-sm text-slate-900 placeholder-slate-400 focus:border-[#1E40AF] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#1E40AF]/10 transition-all" autocomplete="username" autofocus required>
                        @error('username')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <!-- Input Password -->
                    <div>
                        <div class="relative">
                            <input id="password" type="password" name="password" placeholder="Kata Sandi" class="w-full bg-slate-50 border border-transparent rounded-xl px-4 py-4 pr-12 text-sm text-slate-900 placeholder-slate-400 focus:border-[#1E40AF] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#1E40AF]/10 transition-all" autocomplete="current-password" required>
                            
                            <!-- Ikon Mata (Toggle Password) -->
                            <button type="button" id="lihat" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Lupa Password -->
                    <div class="flex justify-end pt-1 pb-2">
                        <a href="#" class="text-xs font-medium text-slate-400 hover:text-[#1E40AF] transition-colors">Lupa Kata Sandi?</a>
                    </div>

                    <!-- Tombol Login -->
                    <button type="submit" class="w-full bg-[#1E40AF] hover:bg-blue-800 text-white rounded-xl py-4 text-sm font-semibold transition-all shadow-lg shadow-blue-900/20 hover:-translate-y-0.5">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
        
        <p class="mt-8 text-center text-sm font-medium text-slate-400">{{ \App\Models\Setting::get('nama_sekolah', 'Ujian Berbasis Komputer') }}</p>
    </div>

    <!-- Script Toggle Ikon Mata Password -->
    <script>
        const p = document.getElementById('password'), b = document.getElementById('lihat');
        b.onclick = () => {
            const s = p.type === 'password';
            p.type = s ? 'text' : 'password';
            
            if(s) {
                b.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>`;
            } else {
                b.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>`;
            }
        };
    </script>
</body>
</html>