@extends('layouts.app')
@section('title', 'Monitor Ujian')
@section('content')
<div class="card">
    <form class="flex flex-col gap-2 sm:flex-row">
        <select name="bank" class="input sm:w-80" onchange="this.form.submit()">@foreach ($banks as $b)<option value="{{ $b->id }}" @selected($bank?->id === $b->id)>{{ $b->mapel }} — {{ $b->jenis_ujian }} (Kls {{ $b->kelas }} {{ $b->rombel }})</option>@endforeach</select>
        <input name="q" value="{{ request('q') }}" placeholder="Cari nama siswa…" class="input sm:w-64">
        <button class="btn btn-primary">Cari</button>
        <label class="flex items-center gap-2 text-sm sm:ml-auto"><input type="checkbox" id="auto" checked> Perbarui otomatis (10 dtk)</label>
    </form>
</div>
<div class="card">
    @if(!$bank)<p class="py-6 text-center text-slate-400">Belum ada ujian aktif.</p>@else
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>Nama</th><th>Rombel</th><th>Status</th><th>Progres</th><th>Pelanggaran</th><th>Sisa waktu</th><th></th></tr></thead>
            <tbody>
            @foreach ($rows as $s)
                @php $u = $uj[$s->id] ?? null; $pr = $u && $total ? round($u->jumlahTerjawab() / $total * 100) : 0; @endphp
                <tr><td class="{{ $u?->curang ? 'font-semibold text-red-600' : '' }}">{{ $s->name }}</td><td>{{ $s->kelas }} {{ $s->rombel }}</td>
                    <td>@if(!$u)<span class="badge bg-slate-100 text-slate-500">Belum mengerjakan</span>
                        @elseif($u->status === 'selesai')<span class="badge bg-green-50 text-green-700">Selesai</span>
                        @else<span class="badge bg-indigo-50 text-indigo-700">Sedang mengerjakan</span>@endif</td>
                    <td class="min-w-32">@if($u)<div class="flex items-center gap-2"><div class="h-2 w-20 rounded bg-slate-100"><div class="h-2 rounded bg-brand transition-all duration-700" style="width: {{ $pr }}%"></div></div><span class="text-xs">{{ $u->jumlahTerjawab() }}/{{ $total }}</span></div>@else — @endif</td>
                    <td>{{ $u?->pelanggaran ?? 0 }}</td>
                    <td>@if($u && $u->status !== 'selesai'){{ gmdate('H:i:s', $u->sisaDetik()) }}@else — @endif</td>
                    <td class="space-x-1 whitespace-nowrap text-right">
                        @if($u && $u->status !== 'selesai')<form method="POST" action="{{ route('admin.monitor.selesai', $u) }}" class="inline" onsubmit="return confirm('Selesaikan ujian siswa ini? Jawaban yang sudah ada tetap dinilai.')">@csrf<button class="btn btn-outline btn-sm">Selesaikan</button></form>@endif
                        @if($u)<form method="POST" action="{{ route('admin.monitor.reset', $u) }}" class="inline" onsubmit="return confirm('Reset ujian siswa? Seluruh jawaban dan hasilnya dihapus dan waktu kembali ke durasi awal.')">@csrf<button class="btn btn-danger btn-sm">Reset</button></form>@endif</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>@endif
</div>
@push('scripts')
<script>setInterval(() => { if (document.getElementById('auto').checked && !document.hidden) location.reload(); }, 10000);</script>
@endpush
@endsection
