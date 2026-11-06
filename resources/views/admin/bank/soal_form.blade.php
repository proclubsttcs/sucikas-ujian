@extends('layouts.app')
@section('title', $soal->exists ? 'Edit Soal' : 'Tambah Soal')
@section('content')
@php
    $tipe = old('tipe', $soal->tipe ?: 'pg');
    $opsi = $soal->opsi ?? [];
    $kunci = $soal->kunci;
    $pasangan = $soal->tipe === 'jodoh' ? collect($opsi)->map(fn ($p) => $p['kiri'].'|'.$p['kanan'])->implode("\n") : '';
@endphp
<form method="POST" enctype="multipart/form-data" class="card max-w-3xl space-y-4"
      action="{{ $soal->exists ? route('admin.soal.update', $soal) : route('admin.soal.store', $bank) }}">
    @csrf @if($soal->exists) @method('PUT') @endif
    <div class="grid gap-3 sm:grid-cols-2">
        <div><label class="label">Jenis soal</label>
            <select name="tipe" id="tipe" class="input">@foreach (\App\Models\BankSoal::TIPE as $k => $v)<option value="{{ $k }}" @selected($tipe === $k)>{{ $v }}</option>@endforeach</select></div>
        <div><label class="label">Nomor soal</label><input type="number" name="nomor" value="{{ old('nomor', $soal->nomor) }}" min="1" class="input" required></div>
    </div>
    <div><label class="label">Pertanyaan</label><textarea name="pertanyaan" rows="4" class="input" required>{{ old('pertanyaan', $soal->pertanyaan) }}</textarea></div>
    <div class="grid gap-3 sm:grid-cols-2">
        <div><label class="label">Gambar soal @if($soal->gambar)<span class="font-normal text-slate-500">({{ $soal->gambar }})</span>@endif</label><input type="file" name="gambar" accept="image/*" class="input">
            @if($soal->gambar)<label class="mt-1 flex items-center gap-1 text-xs"><input type="checkbox" name="hapus_gambar" value="1"> Hapus gambar</label>@endif</div>
        <div><label class="label">Audio soal @if($soal->audio)<span class="font-normal text-slate-500">({{ $soal->audio }})</span>@endif</label><input type="file" name="audio" accept="audio/*" class="input">
            @if($soal->audio)<label class="mt-1 flex items-center gap-1 text-xs"><input type="checkbox" name="hapus_audio" value="1"> Hapus audio</label>@endif</div>
    </div>

    <div data-tipe="pg pgk" class="space-y-2">
        <label class="label">Opsi jawaban & kunci</label>
        @foreach (array_slice(['A', 'B', 'C', 'D', 'E'], 0, $bank->jumlah_opsi) as $L)
            <div class="flex items-center gap-2">
                <span class="w-6 font-semibold">{{ $L }}</span>
                <input name="opsi[{{ $L }}]" value="{{ old("opsi.$L", $opsi[$L]['t'] ?? '') }}" class="input" placeholder="Teks opsi {{ $L }}">
                <label class="flex shrink-0 items-center gap-1 text-xs" data-tipe="pg"><input type="radio" name="kunci_pg" value="{{ $L }}" @checked($kunci === $L && $soal->tipe === 'pg')> kunci</label>
                <label class="flex shrink-0 items-center gap-1 text-xs" data-tipe="pgk"><input type="checkbox" name="kunci_pgk[]" value="{{ $L }}" @checked($soal->tipe === 'pgk' && in_array($L, (array) $kunci))> kunci</label>
            </div>
        @endforeach
    </div>
    <div data-tipe="jodoh"><label class="label">Pasangan (satu per baris, format <code>kiri|kanan</code>)</label>
        <textarea name="pasangan" rows="5" class="input font-mono" placeholder="Segitiga|3&#10;Persegi|4">{{ old('pasangan', $pasangan) }}</textarea></div>
    <div data-tipe="bs"><label class="label">Kunci jawaban</label>
        <label class="mr-4 text-sm"><input type="radio" name="kunci_bs" value="benar" @checked($kunci === 'benar')> Benar</label>
        <label class="text-sm"><input type="radio" name="kunci_bs" value="salah" @checked($kunci === 'salah')> Salah</label></div>
    <p data-tipe="uraian" class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">Soal uraian dikoreksi manual oleh guru di menu Hasil Ujian.</p>

    <div class="flex gap-2"><button class="btn btn-primary">{{ $soal->exists ? 'Simpan' : 'Tambah baru' }}</button><a href="{{ route('admin.bank.show', $bank) }}" class="btn btn-outline">Batal</a></div>
</form>
@push('scripts')
<script>
    const sel = document.getElementById('tipe');
    const sync = () => document.querySelectorAll('[data-tipe]').forEach(el => el.classList.toggle('hidden', !el.dataset.tipe.split(' ').includes(sel.value)));
    sel.onchange = sync; sync();
</script>
@endpush
@endsection
