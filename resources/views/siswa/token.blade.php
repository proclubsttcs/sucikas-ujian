@extends('layouts.siswa')
@section('title', 'Token Ujian')
@section('content')
<div class="mx-auto max-w-sm pt-4">
    <div class="card space-y-5 pt-0 text-center">
        <div class="-mx-5 h-20 rounded-t-2xl bg-brand opacity-90 sm:-mx-5"></div>
        <div class="pop relative mx-auto -mt-16 h-24 w-24 overflow-hidden rounded-full bg-white ring-4 ring-white shadow-xl">
            @if(auth()->user()->fotoUrl())<img src="{{ auth()->user()->fotoUrl() }}" class="h-full w-full object-cover">
            @else<div class="bg-brand-soft grid h-full w-full place-items-center text-3xl font-extrabold text-primary">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>@endif
        </div>
        <div><p class="text-lg font-bold">{{ auth()->user()->name }}</p><p class="text-sm text-slate-500">NIS {{ auth()->user()->username }} · Kelas {{ auth()->user()->kelas }} {{ auth()->user()->rombel }}</p></div>
        <form method="POST" action="{{ route('siswa.token') }}" class="space-y-3 text-left">
            @csrf
            <label class="label text-center">Masukkan token ujian dari sekolah</label>
            <input name="token" class="input py-3 text-center font-mono text-2xl font-bold uppercase tracking-[0.4em]" maxlength="10" autofocus required>
            <button class="btn btn-primary w-full py-3">Lanjutkan →</button>
        </form>
    </div>
</div>
@endsection
