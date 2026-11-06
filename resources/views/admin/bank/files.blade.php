@extends('layouts.app')
@section('title', 'Gambar & Audio — '.$bank->kode)
@section('content')
<form method="POST" action="{{ route('admin.bank.upload', $bank) }}" enctype="multipart/form-data" class="card flex flex-col gap-2 sm:flex-row sm:items-center">
    @csrf<input type="file" name="files[]" multiple accept="image/*,audio/*" class="input" required><button class="btn btn-primary shrink-0">Upload file</button>
    <a href="{{ route('admin.bank.show', $bank) }}" class="btn btn-outline">Kembali</a>
</form>
<p class="text-xs text-slate-500">Tulis nama file persis (termasuk ekstensi) di kolom gambar/audio pada template Excel soal.</p>
<form method="POST" action="{{ route('admin.bank.filedel', $bank) }}" class="card" onsubmit="return confirm('Hapus file terpilih?')">
    @csrf @method('DELETE')
    <div class="mb-3 flex items-center justify-between"><h2 class="font-semibold">{{ $files->count() }} file</h2><button class="btn btn-danger btn-sm">Hapus terpilih</button></div>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-6">
        @forelse ($files as $f)
            @php $url = Storage::url("soal/{$bank->id}/$f"); $audio = preg_match('/\.(mp3|wav|ogg|m4a)$/i', $f); @endphp
            <label class="block text-xs">
                @if($audio)<audio controls src="{{ $url }}" class="w-full"></audio>@else<img src="{{ $url }}" class="aspect-square w-full rounded-lg border border-slate-200 object-cover">@endif
                <input type="checkbox" name="files[]" value="{{ $f }}"> <span class="break-all">{{ $f }}</span></label>
        @empty<p class="col-span-full py-6 text-center text-slate-400">Belum ada file.</p>@endforelse
    </div>
</form>
@endsection
