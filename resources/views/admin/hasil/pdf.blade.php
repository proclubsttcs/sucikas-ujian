<!DOCTYPE html><html><head><meta charset="utf-8">
<style>body{font-family:DejaVu Sans,sans-serif;font-size:10px;color:#0F172A} h2{color:#1E40AF;margin:0} td,th{padding:3px 5px;border:1px solid #E2E8F0;vertical-align:top;text-align:left} table{width:100%;border-collapse:collapse} .ok{color:#15803d}.no{color:#b91c1c}</style></head><body>
@if($logo)<img src="{{ $logo }}" height="36" style="float:right">@endif
<h2>Hasil Ujian — {{ $u->bank->mapel }}</h2>
<p>{{ $u->user->name }} · NIS {{ $u->user->username }} · Kelas {{ $u->user->kelas }} {{ $u->user->rombel }}<br>
Selesai: {{ $u->selesai_at?->format('d/m/Y H:i') }} · Benar {{ $u->jml_benar }} · Salah {{ $u->jml_salah }} · <b>Nilai {{ $u->nilai + 0 }}</b></p>
<table><thead><tr><th>No</th><th>Jenis</th><th>Soal</th><th>Jawaban</th><th>Kunci</th><th>Hasil</th></tr></thead><tbody>
@foreach ($soals as $s)
    @php $j = ($u->jawaban ?? [])[$s->id] ?? null; $ok = $s->benar($j); @endphp
    <tr><td>{{ $s->nomor }}</td><td>{{ \App\Models\BankSoal::TIPE[$s->tipe] }}</td><td>{{ \Illuminate\Support\Str::limit($s->pertanyaan, 160) }}</td>
        <td>{{ $s->jawabanTeks($j) }}</td><td>{{ $s->kunciTeks() }}</td>
        <td>@if($s->tipe === 'uraian')Skor {{ ($u->skor_uraian ?? [])[$s->id] ?? 0 }}@else<span class="{{ $ok ? 'ok' : 'no' }}">{{ $ok ? 'Benar' : 'Salah' }}</span>@endif</td></tr>
@endforeach
</tbody></table></body></html>
