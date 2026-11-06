@php $tidak = $siswa->count() - $hadir; @endphp
    <div style="text-align:center;border-bottom:2px solid #000;padding-bottom:8px;margin-bottom:12px">
        <b style="font-size:14pt">BERITA ACARA PELAKSANAAN {{ strtoupper($bank->jenis_ujian) }}</b><br>
        <b>{{ $s['nama_sekolah'] }}</b><br>Tahun Pelajaran {{ $s['tahun_ajaran'] }}
    </div>
    <p>Pada hari ini {{ $tanggal->translatedFormat('l') }} tanggal {{ $tanggal->translatedFormat('d F Y') }} telah diselenggarakan {{ $bank->jenis_ujian }} berbasis komputer, dengan rincian sebagai berikut:</p>
    <table style="margin:8px 0">
        <tr><td>Mata pelajaran</td><td>: {{ $bank->mapel }}</td></tr>
        <tr><td>Kelas / rombel</td><td>: {{ $kelas }} {{ $rombel }}</td></tr>
        <tr><td>Waktu pelaksanaan</td><td>: {{ $mulai ? \Carbon\Carbon::parse($mulai)->format('H:i') : '..........' }} s.d. {{ $selesai ? \Carbon\Carbon::parse($selesai)->format('H:i') : '..........' }} WIB (alokasi {{ $bank->durasi }} menit)</td></tr>
        <tr><td>Jumlah peserta terdaftar</td><td>: {{ $siswa->count() }} siswa</td></tr>
        <tr><td>Jumlah peserta hadir</td><td>: {{ $hadir }} siswa</td></tr>
        <tr><td>Jumlah peserta tidak hadir</td><td>: {{ $tidak }} siswa</td></tr>
        <tr><td>Peserta terindikasi curang</td><td>: {{ $uj->where('curang', true)->count() }} siswa</td></tr>
    </table>
    <p>Catatan selama pelaksanaan ujian: ..............................................................................................</p>
    <p>Demikian berita acara ini dibuat dengan sebenarnya.</p>
    <table style="width:100%;margin-top:24px"><tr>
        <td style="width:50%;text-align:center">Pengawas,<br><br><br><br>(...............................)</td>
        <td style="width:50%;text-align:center">{{ $s['kota'] }}, {{ $tanggal->translatedFormat('d F Y') }}<br>Kepala Sekolah,<br>
            @if($ttd && !$doc)<img src="{{ $ttd }}" style="height:56px;margin:0 auto"><br>@else<br><br><br>@endif
            <b><u>{{ $s['kepala_sekolah'] }}</u></b><br>NIP. {{ $s['nip_kepsek'] }}</td>
    </tr></table>
