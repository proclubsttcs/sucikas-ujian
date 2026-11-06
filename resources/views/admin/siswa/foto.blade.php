@extends('layouts.app')
@section('title', 'Foto Siswa')
@section('content')
<form method="POST" action="{{ route('admin.siswa.foto.upload') }}" enctype="multipart/form-data" class="card flex flex-col gap-2 sm:flex-row sm:items-center">
    @csrf<input type="file" name="fotos[]" multiple accept="image/*" class="input" required><button class="btn btn-primary shrink-0">Upload foto</button>
</form>
<form method="POST" action="{{ route('admin.siswa.foto.hapus') }}" class="card" onsubmit="return confirm('Hapus foto terpilih?')">
    @csrf @method('DELETE')
    <div class="mb-3 flex items-center justify-between"><h2 class="font-semibold">{{ $files->count() }} foto</h2><button class="btn btn-danger btn-sm">Hapus terpilih</button></div>
    <div class="grid grid-cols-3 gap-3 sm:grid-cols-5 lg:grid-cols-8">
        @forelse ($files as $f)
            <label class="block cursor-pointer text-center text-xs"><img src="{{ Storage::url('siswa/'.$f) }}" class="aspect-square w-full rounded-lg border border-slate-200 object-cover">
                <input type="checkbox" name="files[]" value="{{ $f }}" class="mt-1"> <span class="break-all">{{ $f }}</span></label>
        @empty<p class="col-span-full py-6 text-center text-slate-400">Belum ada foto.</p>@endforelse
    </div>
</form>
@endsection
