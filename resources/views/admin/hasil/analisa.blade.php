@extends('layouts.app')
@section('title', 'Analisa Soal — '.$bank->mapel)
@section('content')
<div class="flex"><a href="{{ route('admin.hasil.bank', $bank) }}" class="btn btn-outline">Kembali</a></div>
<div class="card">
    <p class="mb-3 text-sm text-slate-500">Dihitung dari {{ $n }} siswa yang telah selesai. Persentase rendah menandakan soal sulit atau perlu ditinjau ulang.</p>
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>No</th><th>Soal</th><th>Jenis</th><th>Benar</th><th>Salah</th><th>Tidak dijawab</th><th>% Benar</th></tr></thead>
            <tbody>
            @foreach ($rows as $r)
                <tr><td>{{ $r['s']->nomor }}</td><td class="max-w-md">{{ \Illuminate\Support\Str::limit(strip_tags($r['s']->pertanyaan), 100) }}</td>
                    <td>{{ \App\Models\BankSoal::TIPE[$r['s']->tipe] }}</td><td>{{ $r['benar'] }}</td><td>{{ $r['salah'] }}</td><td>{{ $r['kosong'] }}</td>
                    <td>@if($r['persen'] === null)<span class="text-slate-400">manual</span>@else
                        <div class="flex items-center gap-2"><div class="h-2 w-24 overflow-hidden rounded bg-slate-100"><div class="bar-x h-2 rounded {{ $r['persen'] < 40 ? 'bg-red-400' : ($r['persen'] < 70 ? 'bg-amber-400' : 'bg-green-500') }}" style="width: {{ $r['persen'] }}%"></div></div>{{ $r['persen'] }}%</div>@endif</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
