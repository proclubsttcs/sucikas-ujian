@extends('layouts.siswa')
@section('title', 'Ujian Selesai')
@section('content')
<div class="card mx-auto max-w-md space-y-4 py-8 text-center">
    <div class="pop mx-auto grid h-20 w-20 place-items-center rounded-full text-4xl text-white shadow-xl" style="background:linear-gradient(135deg,#10b981,#14b8a6);animation:pop .5s both,pulse-ring 2s 1s ease-out infinite">✓</div>
    <h1 class="text-xl font-extrabold tracking-tight">Ujian telah dikumpulkan</h1>
    <p class="text-sm text-slate-600">{{ $ujian->bank->mapel }} — jawaban Anda sudah tersimpan di sistem.</p>
    @if ($ujian->curang)<p class="rounded-xl bg-rose-50 p-3 text-sm text-rose-700">Ujian dikumpulkan otomatis karena Anda meninggalkan halaman ujian.</p>@endif
    @if ($tampil)<div><p class="text-6xl font-extrabold text-brand" data-count="{{ $ujian->nilai + 0 }}">0</p><p class="mt-1 text-xs text-slate-500">Nilai sementara (soal uraian dikoreksi guru)</p></div>@endif
    <a href="{{ route('siswa.daftar') }}" class="btn btn-primary">Kembali ke daftar ujian</a>
</div>
@push('scripts')
<script>
    document.querySelectorAll('[data-count]').forEach(el => { const to = parseFloat(el.dataset.count) || 0, t0 = performance.now();
        const step = t => { const p = Math.min(1, (t - t0) / 1100); el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3))); if (p < 1) requestAnimationFrame(step); }; requestAnimationFrame(step); });
</script>
@endpush
@endsection
