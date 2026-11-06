@extends('layouts.app')
@section('title', 'Hasil — '.$bank->mapel)
@section('content')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.hasil.export', $bank) }}" class="btn btn-outline">Ekspor Excel</a>
    <a href="{{ route('admin.hasil.export', [$bank, 'format' => 'csv']) }}" class="btn btn-outline">Ekspor CSV</a>
    <a href="{{ route('admin.hasil.analisa', $bank) }}" class="btn btn-outline">Analisa soal</a>
    <form method="POST" action="{{ route('admin.hasil.koreksi', $bank) }}" onsubmit="return confirm('Hitung ulang semua nilai dengan kunci jawaban terbaru?')">@csrf<button class="btn btn-primary">Koreksi ulang</button></form>
    <a href="{{ route('admin.hasil') }}" class="btn btn-outline ml-auto">Kembali</a>
</div>
<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    @foreach ([['Jumlah siswa', $stat['jumlah']], ['Nilai rata-rata', $stat['rata']], ['Tertinggi', $stat['max']], ['Terendah', $stat['min']]] as [$l, $v])
        <div class="card"><p class="text-xs text-slate-500">{{ $l }}</p><p class="mt-1 text-2xl font-semibold">{{ $v }}</p></div>
    @endforeach
</div>
<div class="card">
    <h2 class="mb-3 font-semibold">Sebaran nilai</h2>
    @php $mx = max(1, $dist->max()); @endphp
    <div class="flex h-40 items-end gap-1 sm:gap-2">
        @foreach ($dist as $i => $n)
            <div class="flex flex-1 flex-col items-center justify-end gap-1">
                <span class="text-xs text-slate-500">{{ $n }}</span>
                <div class="bar-y w-full rounded-t-lg" style="height: {{ $n / $mx * 100 }}%; min-height: {{ $n ? 4 : 1 }}px; background-image: linear-gradient(to top, var(--color-primary), var(--color-accent)); animation-delay: {{ $i * 60 }}ms"></div>
                <span class="text-[10px] text-slate-500">{{ $i * 10 }}{{ $i == 9 ? '+' : '' }}</span>
            </div>
        @endforeach
    </div>
</div>
<div class="card">
    <input id="cari" placeholder="Cari nama siswa…" class="input mb-3 sm:w-64">
    <p class="mb-2 text-xs text-slate-500">Nilai otomatis dari soal PG, PG kompleks, menjodohkan, dan benar/salah; nilai uraian ditambahkan setelah dikoreksi guru. Nama merah = terindikasi curang (auto submit).</p>
    <div class="overflow-x-auto">
        <table class="tbl" id="tabel">
            <thead><tr><th>Nama</th><th>Rombel</th><th>Tanggal</th><th>Mapel</th><th>Soal</th><th>Uraian</th><th>Benar</th><th>Salah</th><th>Nilai</th><th></th></tr></thead>
            <tbody>
            @foreach ($rows as $u)
                <tr><td class="{{ $u->curang ? 'font-semibold text-red-600' : '' }}">{{ $u->user->name }} @if($u->curang)<span class="badge bg-red-50 text-red-600">curang</span>@endif</td>
                    <td>{{ $u->user->kelas }} {{ $u->user->rombel }}</td><td>{{ $u->selesai_at?->format('d/m/Y') }}</td><td>{{ $bank->mapel }}</td>
                    <td>{{ $u->total_soal }}</td><td>{{ $u->jml_uraian }}</td><td>{{ $u->jml_benar }}</td><td>{{ $u->jml_salah }}</td><td class="font-semibold">{{ $u->nilai + 0 }}</td>
                    <td class="space-x-1 whitespace-nowrap text-right">
                        <a href="{{ route('admin.hasil.detail', $u) }}" class="btn btn-outline btn-sm">Detail</a>
                        <form method="POST" action="{{ route('admin.hasil.reset', $u) }}" class="inline" onsubmit="return confirm('Hapus hasil siswa ini agar bisa mengulang?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Reset</button></form></td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@push('scripts')
<script>document.getElementById('cari').oninput = e => document.querySelectorAll('#tabel tbody tr').forEach(r => r.hidden = !r.textContent.toLowerCase().includes(e.target.value.toLowerCase()));</script>
@endpush
@endsection
