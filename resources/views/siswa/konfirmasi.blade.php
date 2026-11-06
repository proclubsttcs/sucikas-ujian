@extends('layouts.siswa')
@section('title', 'Konfirmasi Ujian')
@section('content')
<div class="card space-y-5">
    <h1 class="text-xl font-extrabold tracking-tight">Konfirmasi data ujian</h1>
    <dl class="bg-brand-soft grid grid-cols-3 gap-y-3 rounded-2xl p-4 text-sm">
        <dt class="text-slate-500">Mata pelajaran</dt><dd class="col-span-2 font-semibold">{{ $bank->mapel }}</dd>
        <dt class="text-slate-500">Jenis ujian</dt><dd class="col-span-2 font-semibold">{{ $bank->jenis_ujian }}</dd>
        <dt class="text-slate-500">Waktu</dt><dd class="col-span-2 font-semibold">{{ $bank->durasi }} menit</dd>
        <dt class="text-slate-500">Nama</dt><dd class="col-span-2 font-semibold">{{ auth()->user()->name }}</dd>
    </dl>
    <form method="POST" action="{{ route('siswa.mulai', $bank) }}" class="space-y-4">
        @csrf
        <div class="space-y-2">
            @foreach (['Saya akan mengerjakan ujian dengan jujur dan mandiri.', 'Saya tidak akan berpindah tab / keluar dari aplikasi selama ujian' . ($bank->auto_submit ? ' (maksimal '.$bank->auto_submit.' peringatan, setelah itu ujian otomatis dikumpulkan).' : '.'), 'Saya memastikan koneksi internet dan baterai perangkat memadai.'] as $t)
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3 text-sm transition hover:border-primary hover:bg-indigo-50/50 has-[:checked]:border-primary has-[:checked]:bg-indigo-50"><input type="checkbox" class="syarat mt-0.5 h-4 w-4 accent-indigo-600"> <span>{{ $t }}</span></label>
            @endforeach
        </div>
        <div class="flex gap-2"><button id="mulai" class="btn btn-primary flex-1 py-3" disabled>Mulai ujian →</button><a href="{{ route('siswa.daftar') }}" class="btn btn-outline">Kembali</a></div>
    </form>
</div>
@push('scripts')
<script>
    const cb = [...document.querySelectorAll('.syarat')], bt = document.getElementById('mulai');
    cb.forEach(c => c.onchange = () => bt.disabled = !cb.every(x => x.checked));
</script>
@endpush
@endsection
