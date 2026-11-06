<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8">
<style>
    @page { margin: 14mm 10mm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #0F172A; }
    table.g { width: 100%; border-collapse: separate; border-spacing: 6px; }
    td.c { width: 50%; border: 1px solid #1E40AF; padding: 0; vertical-align: top; }
    .h { background: #1E40AF; color: #fff; padding: 5px 7px; font-weight: bold; }
    .b { padding: 6px 7px; }
    .b td { padding: 1.5px 0; vertical-align: top; }
    .tk { font-weight: bold; color: #1E40AF; }
</style></head><body>
@foreach ($siswa->chunk(8) as $page)
    <table class="g">
    @foreach ($page->chunk(2) as $row)
        <tr>
        @foreach ($row as $sw)
            @php
                $foto = null;
                if ($sw->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/'.$sw->foto)) {
                    $p = \Illuminate\Support\Facades\Storage::disk('public')->path('siswa/'.$sw->foto);
                    $foto = 'data:'.mime_content_type($p).';base64,'.base64_encode(file_get_contents($p));
                }
            @endphp
            <td class="c">
                <div class="h"><table width="100%"><tr>
                    @if($logo)<td width="26"><img src="{{ $logo }}" height="22"></td>@endif
                    <td>KARTU PESERTA UJIAN<br>{{ $cfg['nama_sekolah'] }} · TA {{ $cfg['tahun_ajaran'] }}</td></tr></table></div>
                <div class="b"><table width="100%"><tr>
                    <td width="48">@if($foto)<img src="{{ $foto }}" width="42" height="54">@endif</td>
                    <td><table>
                        <tr><td width="52">Nama</td><td>: <b>{{ $sw->name }}</b></td></tr>
                        <tr><td>NIS / User</td><td>: {{ $sw->username }}</td></tr>
                        <tr><td>Kelas</td><td>: {{ $sw->kelas }} {{ $sw->rombel }} {{ $sw->jurusan }}</td></tr>
                        <tr><td>Password</td><td>: <span class="tk">{{ $sw->passwordKartu() }}</span></td></tr>
                    </table></td></tr></table></div>
            </td>
        @endforeach
        @if ($row->count() < 2)<td class="c" style="border:0"></td>@endif
        </tr>
    @endforeach
    </table>
    @if (!$loop->last)<div style="page-break-after: always"></div>@endif
@endforeach
</body></html>
