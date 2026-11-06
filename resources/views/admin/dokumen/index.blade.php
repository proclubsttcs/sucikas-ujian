@extends('layouts.app')
@section('title', 'Kartu Peserta, Daftar Hadir & Berita Acara')
@section('content')
<div class="no-print flex gap-1 rounded-lg bg-white p-1 shadow-sm ring-1 ring-slate-200 sm:w-fit">
    @foreach (['kartu' => 'Kartu peserta', 'hadir' => 'Daftar hadir', 'berita' => 'Berita acara'] as $k => $v)
        <a href="{{ route('admin.dokumen', ['tab' => $k]) }}" class="flex-1 rounded-md px-4 py-1.5 text-center text-sm {{ $tab === $k ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-50' }}">{{ $v }}</a>
    @endforeach
</div>

@if ($tab === 'kartu')
<div class="card">
    <p class="mb-3 text-sm text-slate-600">Kartu dibuat otomatis per kelas. Nama sekolah, logo, dan tanda tangan diatur di menu Pengaturan.</p>
    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($kelas as $k)
            <a target="_blank" href="{{ route('admin.dokumen.kartu', ['kelas' => $k->kelas, 'rombel' => $k->rombel]) }}" class="rounded-lg border border-slate-200 p-4 hover:border-secondary hover:bg-indigo-50">
                <p class="font-semibold">Kelas {{ $k->kelas }} {{ $k->rombel }}</p><p class="text-xs text-slate-500">Cetak kartu (PDF)</p></a>
        @empty<p class="text-slate-400">Belum ada data siswa.</p>@endforelse
    </div>
</div>
@else
<div class="card space-y-3">
    @if ($tab === 'hadir')
        <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3">
            <p class="text-sm">Absensi dengan sistem: <b>{{ $absensi ? 'Aktif' : 'Nonaktif' }}</b> <span class="text-slate-500">— kehadiran terekam otomatis saat siswa mengerjakan ujian.</span></p>
            <form method="POST" action="{{ route('admin.dokumen.absensi') }}">@csrf<button class="btn btn-outline btn-sm">{{ $absensi ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
        </div>
    @endif
    <form method="GET" action="{{ route($tab === 'hadir' ? 'admin.dokumen.hadir' : 'admin.dokumen.berita') }}" class="grid gap-3 sm:grid-cols-4" target="_blank">
        <div><label class="label">Kelas / rombel</label>
            <select name="kelas" class="input" onchange="this.form.rombel.value=this.selectedOptions[0].dataset.r" required>
                <option value="">Pilih…</option>@foreach ($kelas as $k)<option value="{{ $k->kelas }}" data-r="{{ $k->rombel }}">{{ $k->kelas }} {{ $k->rombel }}</option>@endforeach</select>
            <input type="hidden" name="rombel"></div>
        <div><label class="label">Mata pelajaran</label><select name="bank" class="input" required>@foreach ($banks as $b)<option value="{{ $b->id }}">{{ $b->mapel }} ({{ $b->kode }})</option>@endforeach</select></div>
        <div><label class="label">Tanggal</label><input type="date" name="tanggal" value="{{ $hariIni }}" class="input" required></div>
        <div class="flex items-end"><button class="btn btn-primary w-full">Terapkan</button></div>
    </form>
</div>
@endif
@endsection
