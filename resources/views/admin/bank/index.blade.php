@extends('layouts.app')
@section('title', 'Bank Soal')
@section('content')
<div class="card">
    <h2 class="mb-3 font-semibold">Tambah bank soal</h2>
    <form method="POST" action="{{ route('admin.bank.store') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @csrf
        <div><label class="label">Jenis ujian</label><input name="jenis_ujian" class="input" placeholder="PTS / PAS / Ujian Sekolah" required></div>
        <div><label class="label">Kode</label><input name="kode" class="input" required></div>
        <div><label class="label">Mata pelajaran</label><input name="mapel" class="input" required></div>
        <div><label class="label">Waktu (menit)</label><input type="number" name="durasi" value="60" min="1" class="input" required></div>
        <div><label class="label">Kelas</label><input name="kelas" class="input" required></div>
        <div><label class="label">Jurusan (kosong = universal)</label><input name="jurusan" class="input"></div>
        <div><label class="label">Rombel (kosong = semua)</label><input name="rombel" class="input"></div>
        <div><label class="label">Peringatan auto submit (0 = nonaktif)</label><input type="number" name="auto_submit" value="3" min="0" max="10" class="input" required></div>
        <div><label class="label">Jumlah opsi</label><select name="jumlah_opsi" class="input"><option value="4">4 opsi (A–D)</option><option value="5">5 opsi (A–E)</option></select></div>
        <label class="flex items-end gap-2 pb-2 text-sm"><input type="checkbox" name="acak" value="1"> Acak urutan soal</label>
        <div class="flex items-end"><button class="btn btn-primary w-full">Tambah baru</button></div>
    </form>
</div>
<div class="card">
    <form class="mb-3 flex gap-2"><input name="q" value="{{ request('q') }}" placeholder="Cari kode / mapel…" class="input sm:w-64"><button class="btn btn-primary">Cari</button></form>
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>Kode</th><th>Mapel</th><th>Jenis</th><th>Kelas</th><th>Waktu</th><th>Auto submit</th><th>Soal</th><th>Opsi</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($banks as $b)
                <tr><td class="font-mono">{{ $b->kode }}</td><td>{{ $b->mapel }}</td><td>{{ $b->jenis_ujian }}</td>
                    <td>{{ $b->kelas }} {{ $b->rombel }} {{ $b->jurusan }}</td><td>{{ $b->durasi }}′</td>
                    <td>{{ $b->auto_submit ?: 'Nonaktif' }}</td><td>{{ $b->soals_count }}</td>
                    <td>{{ $b->jumlah_opsi }} · {{ $b->acak ? 'Acak' : 'Urut' }}</td>
                    <td><span class="badge {{ $b->aktif ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' }}">{{ $b->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="space-x-1 whitespace-nowrap text-right">
                        <a href="{{ route('admin.bank.show', $b) }}" class="btn btn-outline btn-sm">Detail</a>
                        <form method="POST" action="{{ route('admin.bank.aktif', $b) }}" class="inline">@csrf<button class="btn btn-sm {{ $b->aktif ? 'btn-outline' : 'btn-primary' }}">{{ $b->aktif ? 'Nonaktifkan' : 'Aktifkan soal' }}</button></form>
                        <form method="POST" action="{{ route('admin.bank.destroy', $b) }}" class="inline" onsubmit="return confirm('Hapus bank soal beserta seluruh soal & hasilnya?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>
                    </td></tr>
            @empty<tr><td colspan="10" class="py-6 text-center text-slate-400">Belum ada bank soal.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
