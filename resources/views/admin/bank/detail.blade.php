@extends('layouts.app')
@section('title', 'Detail Bank Soal — '.$bank->kode)
@section('content')
<div class="grid gap-4 lg:grid-cols-3">
    <form method="POST" action="{{ route('admin.bank.update', $bank) }}" class="card grid gap-3 sm:grid-cols-2 lg:col-span-2">
        @csrf @method('PUT')
        <h2 class="font-semibold sm:col-span-2">Pengaturan bank soal</h2>
        <div><label class="label">Jenis ujian</label><input name="jenis_ujian" value="{{ $bank->jenis_ujian }}" class="input" required></div>
        <div><label class="label">Kode</label><input name="kode" value="{{ $bank->kode }}" class="input" required></div>
        <div><label class="label">Mapel</label><input name="mapel" value="{{ $bank->mapel }}" class="input" required></div>
        <div><label class="label">Waktu (menit)</label><input type="number" name="durasi" value="{{ $bank->durasi }}" class="input" required></div>
        <div><label class="label">Kelas</label><input name="kelas" value="{{ $bank->kelas }}" class="input" required></div>
        <div><label class="label">Jurusan</label><input name="jurusan" value="{{ $bank->jurusan }}" class="input"></div>
        <div><label class="label">Rombel</label><input name="rombel" value="{{ $bank->rombel }}" class="input"></div>
        <div><label class="label">Auto submit (0 = nonaktif)</label><input type="number" name="auto_submit" value="{{ $bank->auto_submit }}" min="0" max="10" class="input" required></div>
        <div><label class="label">Jumlah opsi</label><select name="jumlah_opsi" class="input"><option value="4" @selected($bank->jumlah_opsi == 4)>4</option><option value="5" @selected($bank->jumlah_opsi == 5)>5</option></select></div>
        <label class="flex items-end gap-2 pb-2 text-sm"><input type="checkbox" name="acak" value="1" @checked($bank->acak)> Acak soal</label>
        <div class="sm:col-span-2"><button class="btn btn-primary">Simpan pengaturan</button></div>
    </form>
    <form method="POST" action="{{ route('admin.bank.bobot', $bank) }}" class="card space-y-3">
        @csrf
        <h2 class="font-semibold">Bobot per soal</h2>
        @foreach (\App\Models\BankSoal::TIPE as $k => $v)
            <div class="flex items-center justify-between gap-3"><label class="text-sm">{{ $v }}</label><input type="number" step="0.5" min="0" name="bobot[{{ $k }}]" value="{{ $bank->bobotFor($k) }}" class="input w-24"></div>
        @endforeach
        <p class="text-xs text-slate-500">Nilai akhir = poin diperoleh ÷ total poin × 100.</p>
        <button class="btn btn-primary w-full">Simpan bobot</button>
    </form>
</div>

<div class="card space-y-3">
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.soal.create', $bank) }}" class="btn btn-primary">Tambah soal</a>
        <a href="{{ route('admin.soal.template') }}" class="btn btn-outline">Unduh template soal</a>
        <a href="{{ route('admin.bank.files', $bank) }}" class="btn btn-outline">Gambar & audio</a>
        <a href="{{ route('admin.bank.index') }}" class="btn btn-outline ml-auto">Kembali</a>
    </div>
    <form method="POST" action="{{ route('admin.soal.import', $bank) }}" enctype="multipart/form-data" class="flex flex-col gap-2 rounded-lg bg-slate-50 p-3 sm:flex-row">
        @csrf<input type="file" name="file" accept=".xlsx,.xls,.csv" class="input bg-white" required><button class="btn btn-primary shrink-0">Import soal</button>
    </form>
</div>

<div class="card">
    <h2 class="mb-3 font-semibold">Daftar soal ({{ $soals->count() }})</h2>
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>No</th><th>Jenis</th><th>Soal</th><th>Kunci</th><th></th></tr></thead>
            <tbody>
            @forelse ($soals as $s)
                <tr><td>{{ $s->nomor }}</td><td><span class="badge bg-indigo-50 text-indigo-700">{{ \App\Models\BankSoal::TIPE[$s->tipe] }}</span></td>
                    <td class="max-w-md">{{ \Illuminate\Support\Str::limit(strip_tags($s->pertanyaan), 120) }}
                        @if($s->gambar) <span class="badge bg-slate-100 text-slate-600">gambar</span>@endif @if($s->audio) <span class="badge bg-slate-100 text-slate-600">audio</span>@endif</td>
                    <td class="max-w-xs text-slate-600">{{ $s->kunciTeks() }}</td>
                    <td class="space-x-1 whitespace-nowrap text-right">
                        <a href="{{ route('admin.soal.edit', $s) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.soal.destroy', $s) }}" class="inline" onsubmit="return confirm('Hapus soal?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form></td></tr>
            @empty<tr><td colspan="5" class="py-6 text-center text-slate-400">Belum ada soal.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
