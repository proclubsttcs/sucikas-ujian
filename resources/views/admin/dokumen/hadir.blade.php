@extends('layouts.app')
@section('title', 'Daftar Hadir')
@section('content')
<div class="no-print"><button onclick="window.print()" class="btn btn-primary">Cetak</button></div>
<div class="card print:border-0 print:shadow-none">
    <div class="mb-4 flex items-center gap-3 border-b-2 border-slate-800 pb-3">
        @if($logo)<img src="{{ $logo }}" class="h-14">@endif
        <div class="flex-1 text-center"><p class="text-lg font-bold uppercase">Daftar Hadir Peserta Ujian</p><p class="font-semibold">{{ $s['nama_sekolah'] }}</p><p class="text-xs">{{ $s['alamat'] }}</p></div>
    </div>
    <table class="mb-3 text-sm">
        <tr><td class="pr-4">Mata pelajaran</td><td>: {{ $bank->mapel }} ({{ $bank->jenis_ujian }})</td></tr>
        <tr><td>Kelas</td><td>: {{ $kelas }} {{ $rombel }}</td></tr>
        <tr><td>Hari / tanggal</td><td>: {{ $tanggal->translatedFormat('l, d F Y') }}</td></tr>
    </table>
    <table class="tbl border border-slate-300">
        <thead><tr><th class="w-10">No</th><th>Nama</th><th>NIS</th><th class="w-40">Tanda tangan</th><th class="w-24">Ket.</th></tr></thead>
        <tbody>
        @foreach ($siswa as $i => $u)
            @php $hadir = $otomatis && $uj->has($u->id); @endphp
            <tr class="h-9"><td>{{ $i + 1 }}</td><td>{{ $u->name }}</td><td>{{ $u->username }}</td>
                <td>{{ $hadir ? '✔ (terekam sistem)' : '' }}</td><td>{{ $otomatis ? ($hadir ? 'Hadir' : 'Tidak hadir') : '' }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
