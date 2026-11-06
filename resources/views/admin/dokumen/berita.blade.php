@extends('layouts.app')
@section('title', 'Berita Acara Ujian')
@section('content')
<div class="no-print flex gap-2">
    <button onclick="window.print()" class="btn btn-primary">Cetak</button>
    <a class="btn btn-outline" href="{{ request()->fullUrlWithQuery(['doc' => 1]) }}">Unduh Word (.doc)</a>
</div>
<div class="card print:border-0 print:shadow-none" style="font-family: 'Times New Roman', serif">
    @include('admin.dokumen.berita_isi', ['doc' => false])
</div>
@endsection
