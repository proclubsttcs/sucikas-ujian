@if (session('ok'))<div class="toast no-print flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-800 shadow-sm"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-500 text-xs text-white">✓</span>{{ session('ok') }}</div>@endif
@if (session('err'))<div class="toast no-print flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50/90 px-4 py-3 text-sm text-rose-700 shadow-sm"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-rose-500 text-xs text-white">!</span>{{ session('err') }}</div>@endif
@if (session('peringatan'))
    <div class="toast rounded-2xl border border-amber-200 bg-amber-50/90 px-4 py-3 text-sm text-amber-800 shadow-sm">
        <p class="font-semibold">Beberapa baris dilewati:</p>
        <ul class="mt-1 list-disc pl-5">@foreach (array_slice(session('peringatan'), 0, 15) as $m)<li>{{ $m }}</li>@endforeach</ul>
    </div>
@endif
@if ($errors->any())
    <div class="toast rounded-2xl border border-rose-200 bg-rose-50/90 px-4 py-3 text-sm text-rose-700 shadow-sm">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
