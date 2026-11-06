@extends('layouts.app')
@section('title', 'Manajemen Siswa')
@section('content')
<div class="card space-y-3">
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary">Tambah siswa</a>
        <a href="{{ route('admin.siswa.template') }}" class="btn btn-outline">Unduh template Excel</a>
        <a href="{{ route('admin.siswa.foto') }}" class="btn btn-outline">Foto siswa</a>
    </div>
    <form method="POST" action="{{ route('admin.siswa.import') }}" enctype="multipart/form-data" class="flex flex-col gap-2 rounded-lg bg-slate-50 p-3 sm:flex-row sm:items-center">
        @csrf
        <input type="file" name="file" accept=".xlsx,.xls,.csv" class="input bg-white" required>
        <button class="btn btn-primary shrink-0">Import siswa</button>
    </form>
    <p class="text-xs text-slate-500">Template: semua sel format General, NIS harus unik (dipakai untuk login). Kolom foto diisi nama file yang diunggah.</p>
</div>
<div class="card">
    <form class="mb-3 flex flex-col gap-2 sm:flex-row">
        <input name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS…" class="input sm:w-64">
        <select name="kelas" class="input sm:w-40"><option value="">Semua kelas</option>@foreach ($kelasList as $k)<option @selected(request('kelas') == $k)>{{ $k }}</option>@endforeach</select>
        <button class="btn btn-primary">Filter</button>
    </form>
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>Foto</th><th>Nama</th><th>NIS</th><th>Kelas</th><th>Rombel</th><th>Jurusan</th><th>Agama</th><th></th></tr></thead>
            <tbody>
            @forelse ($siswa as $s)
                <tr><td>@if($s->fotoUrl())<img src="{{ $s->fotoUrl() }}" class="h-9 w-9 rounded-full object-cover">@else<div class="grid h-9 w-9 place-items-center rounded-full bg-slate-100 text-xs text-slate-500">{{ strtoupper(substr($s->name, 0, 1)) }}</div>@endif</td>
                    <td>{{ $s->name }}</td><td class="font-mono">{{ $s->nip_nis }}</td><td>{{ $s->kelas }}</td><td>{{ $s->rombel }}</td><td>{{ $s->jurusan }}</td><td>{{ $s->agama }}</td>
                    <td class="space-x-1 whitespace-nowrap text-right">
                        <a href="{{ route('admin.siswa.edit', $s) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.siswa.destroy', $s) }}" class="inline" onsubmit="return confirm('Hapus siswa ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form></td></tr>
            @empty<tr><td colspan="8" class="py-6 text-center text-slate-400">Belum ada data siswa.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $siswa->links() }}</div>
</div>
@endsection
