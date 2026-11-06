@extends('layouts.app')
@section('title', 'Detail Hasil — '.$u->user->name)
@section('content')
@php $bank = $u->bank; $jw = $u->jawaban ?? []; $sk = $u->skor_uraian ?? []; @endphp
<div class="flex flex-wrap items-center gap-2">
    <a href="{{ route('admin.hasil.pdf', $u) }}" class="btn btn-primary">Ekspor PDF</a>
    <a href="{{ route('admin.hasil.bank', $bank) }}" class="btn btn-outline">Kembali</a>
</div>
<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    @foreach ([['Nilai total', $u->nilai + 0], ['Nilai otomatis', $u->nilai_otomatis + 0], ['Nilai uraian', $u->nilai_uraian + 0], ['Benar / Salah', $u->jml_benar.' / '.$u->jml_salah]] as [$l, $v])
        <div class="card"><p class="text-xs text-slate-500">{{ $l }}</p><p class="mt-1 text-xl font-semibold">{{ $v }}</p></div>
    @endforeach
</div>
<form method="POST" action="{{ route('admin.hasil.uraian', $u) }}" class="space-y-3">
    @csrf
    @foreach ($soals as $s)
        @php $j = $jw[$s->id] ?? null; $ok = $s->benar($j); @endphp
        <div class="card">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-semibold">No. {{ $s->nomor }} · {{ \App\Models\BankSoal::TIPE[$s->tipe] }}</span>
                @if($s->tipe !== 'uraian')<span class="badge {{ $ok ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">{{ $ok ? 'Benar' : 'Salah' }}</span>@endif
            </div>
            <p class="whitespace-pre-line text-sm">{{ $s->pertanyaan }}</p>
            <p class="mt-2 text-sm"><span class="text-slate-500">Jawaban siswa:</span> {{ $s->jawabanTeks($j) }}</p>
            @if($s->tipe !== 'uraian')<p class="text-sm"><span class="text-slate-500">Kunci:</span> {{ $s->kunciTeks() }}</p>
            @else
                <div class="mt-2 flex items-center gap-2 text-sm"><label>Nilai (maks {{ $bank->bobotFor('uraian') + 0 }}):</label>
                    <input type="number" step="0.5" min="0" max="{{ $bank->bobotFor('uraian') }}" name="skor[{{ $s->id }}]" value="{{ $sk[$s->id] ?? 0 }}" class="input w-24"></div>
            @endif
        </div>
    @endforeach
    @if ($soals->contains('tipe', 'uraian'))<button class="btn btn-primary">Submit nilai uraian</button>@endif
</form>
@endsection
