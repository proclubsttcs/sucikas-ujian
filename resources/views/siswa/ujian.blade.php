<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian — Sucikas Ujian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen select-none font-sans text-slate-900 antialiased">
<header class="sticky top-0 z-20 border-b border-white/60 bg-white/75 backdrop-blur-xl">
    <div class="mx-auto flex h-14 max-w-5xl items-center gap-2 px-3">
        <p id="judul" class="min-w-0 flex-1 truncate text-sm font-semibold"></p>
        <span id="timer" class="rounded-xl bg-brand px-3.5 py-1.5 font-mono text-sm font-bold text-white shadow-lg shadow-indigo-500/30 transition-colors">--:--:--</span>
        <button id="btn-refresh" class="btn btn-outline btn-sm" title="Muat ulang bila ada kendala koneksi">Refresh</button>
    </div>
</header>
<main class="mx-auto grid max-w-5xl gap-4 p-3 sm:p-4 lg:grid-cols-[1fr_260px]">
    <section class="card order-2 min-h-72 lg:order-1" id="soal" style="padding:1.25rem"></section>
    <aside class="card order-1 h-fit lg:sticky lg:top-20 lg:order-2">
        <p class="mb-2 text-xs font-semibold uppercase text-slate-500">Navigasi soal</p>
        <div id="nav" class="grid grid-cols-8 gap-1.5 sm:grid-cols-10 lg:grid-cols-5"></div>
        <div class="mt-3 flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-slate-500"><span>■ <b class="text-primary">ungu</b>: terjawab</span><span>■ <b class="text-amber-500">kuning</b>: ragu</span></div>
        <button id="btn-selesai" class="btn btn-primary mt-4 w-full">Selesai ujian</button>
    </aside>
</main>
<div id="modal" class="fixed inset-0 z-50 hidden place-items-center bg-slate-900/50 p-4"><div class="card pop w-full max-w-sm space-y-3"><div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-amber-100 text-2xl">⚠️</div><p id="modal-t" class="text-center text-sm"></p><button class="btn btn-primary w-full" onclick="modal.classList.add('hidden');modal.classList.remove('grid')">Mengerti</button></div></div>
<form id="f-selesai" method="POST" action="{{ route('siswa.selesai', $ujian) }}">@csrf</form>

<script>
const D = {{ \Illuminate\Support\Js::from($data) }};
const csrf = document.querySelector('meta[name=csrf-token]').content;
const S = { i: 0, jwb: Object.assign({}, D.jawaban), ragu: new Set(D.ragu.map(Number)) };
let reloading = false;
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const terjawab = id => { const v = S.jwb[id]; return !(v === undefined || v === null || v === '' || (Array.isArray(v) && !v.length) || (typeof v === 'object' && !Array.isArray(v) && !Object.values(v).some(Boolean))); };

async function kirim(url, body) {
    try {
        const r = await fetch(url, { method: 'POST', keepalive: true, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(body || {}) });
        const j = await r.json();
        if (j.selesai) { reloading = true; location.href = j.url; }
        return j;
    } catch (e) { return null; }
}
function simpan(s, val) {
    S.jwb[s.id] = val; renderNav();
    kirim(D.urlJawab, { soal_id: s.id, jawaban: val, ragu: S.ragu.has(s.id) });
}
function tampilModal(t) { $('modal-t').textContent = t; $('modal').classList.remove('hidden'); $('modal').classList.add('grid'); }

function renderNav() {
    $('nav').innerHTML = D.soal.map((s, i) => {
        const cls = i === S.i ? 'ring-2 ring-primary ' : '';
        const warna = S.ragu.has(s.id) ? 'bg-amber-400 text-white' : terjawab(s.id) ? 'bg-brand text-white shadow-md shadow-indigo-500/30' : 'bg-white/80 text-slate-700 border border-slate-200 hover:border-primary';
        return `<button data-i="${i}" class="${cls}${warna} h-9 rounded-lg text-xs font-semibold transition hover:-translate-y-0.5">${i + 1}</button>`;
    }).join('');
    $('nav').querySelectorAll('button').forEach(b => b.onclick = () => { S.i = +b.dataset.i; render(); });
}

function render() {
    const s = D.soal[S.i], n = D.soal.length, cur = S.jwb[s.id];
    let h = `<div class="mb-3 flex items-center justify-between"><span class="text-sm font-semibold text-primary">Soal ${S.i + 1} dari ${n}</span>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="ragu" ${S.ragu.has(s.id) ? 'checked' : ''}> Ragu-ragu</label></div>`;
    h += `<div class="whitespace-pre-line text-[15px] leading-relaxed">${esc(s.q)}</div>`;
    if (s.img) h += `<img src="${esc(s.img)}" class="mt-3 max-h-80 max-w-full rounded-lg border border-slate-200">`;
    if (s.aud) h += `<audio controls controlsList="nodownload" src="${esc(s.aud)}" class="mt-3 w-full"></audio>`;
    h += '<div class="mt-4 space-y-2">';
    if (s.tipe === 'pg' || s.tipe === 'pgk') {
        const multi = s.tipe === 'pgk', sel = multi ? (cur || []) : cur;
        h += (multi ? '<p class="text-xs text-slate-500">Pilih lebih dari satu jawaban yang benar.</p>' : '') + s.opsi.map(o => {
            const on = multi ? sel.includes(o.k) : sel === o.k;
            return `<label class="flex cursor-pointer items-start gap-3 rounded-xl border-2 p-3.5 text-sm transition ${on ? 'border-primary bg-indigo-50 shadow-sm shadow-indigo-500/10' : 'border-slate-200 hover:border-indigo-300 hover:bg-slate-50'}">
                <input type="${multi ? 'checkbox' : 'radio'}" name="o" value="${o.k}" ${on ? 'checked' : ''} class="mt-0.5">
                <span><b>${o.k}.</b> ${esc(o.t)}${o.g ? `<img src="${esc(o.g)}" class="mt-2 max-h-40 rounded">` : ''}</span></label>`;
        }).join('');
    } else if (s.tipe === 'bs') {
        h += ['benar', 'salah'].map(v => `<label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 p-3.5 text-sm transition ${cur === v ? 'border-primary bg-indigo-50 shadow-sm shadow-indigo-500/10' : 'border-slate-200 hover:border-indigo-300 hover:bg-slate-50'}">
            <input type="radio" name="o" value="${v}" ${cur === v ? 'checked' : ''}> ${v === 'benar' ? 'Benar' : 'Salah'}</label>`).join('');
    } else if (s.tipe === 'jodoh') {
        const m = cur || {};
        h += s.kiri.map((k, i) => `<div class="flex flex-col gap-1 rounded-lg border border-slate-200 p-3 text-sm sm:flex-row sm:items-center sm:justify-between">
            <span>${esc(k)}</span><select data-i="${i}" class="input sm:w-56"><option value="">— pilih —</option>${s.kanan.map(r => `<option ${m[i] === r ? 'selected' : ''} value="${esc(r)}">${esc(r)}</option>`).join('')}</select></div>`).join('');
    } else {
        h += `<textarea id="uraian" rows="8" class="input" placeholder="Tulis jawaban Anda di sini…">${esc(cur || '')}</textarea>`;
    }
    h += `</div><div class="mt-5 flex justify-between"><button id="prev" class="btn btn-outline" ${S.i === 0 ? 'disabled' : ''}>← Sebelumnya</button>
        <button id="next" class="btn btn-primary" ${S.i === n - 1 ? 'disabled' : ''}>Selanjutnya →</button></div>`;
    $('soal').innerHTML = h;
    $('soal').classList.remove('fade-up'); void $('soal').offsetWidth; $('soal').classList.add('fade-up');

    $('ragu').onchange = e => { e.target.checked ? S.ragu.add(s.id) : S.ragu.delete(s.id); renderNav(); kirim(D.urlJawab, { soal_id: s.id, jawaban: S.jwb[s.id] ?? null, ragu: e.target.checked }); };
    $('prev').onclick = () => { S.i--; render(); };
    $('next').onclick = () => { S.i++; render(); };
    if (s.tipe === 'pg' || s.tipe === 'bs') $('soal').querySelectorAll('input[name=o]').forEach(r => r.onchange = () => { simpan(s, r.value); render(); });
    if (s.tipe === 'pgk') $('soal').querySelectorAll('input[name=o]').forEach(r => r.onchange = () => { simpan(s, [...$('soal').querySelectorAll('input[name=o]:checked')].map(x => x.value)); render(); });
    if (s.tipe === 'jodoh') $('soal').querySelectorAll('select').forEach(sl => sl.onchange = () => {
        const m = Object.assign({}, S.jwb[s.id] || {}); m[sl.dataset.i] = sl.value; if (!sl.value) delete m[sl.dataset.i];
        simpan(s, Object.keys(m).length ? m : null);
    });
    if (s.tipe === 'uraian') { let t; $('uraian').oninput = e => { clearTimeout(t); t = setTimeout(() => simpan(s, e.target.value.trim() ? e.target.value : null), 600); }; }
    renderNav();
}

// ---- Timer ----
const deadline = Date.now() + D.sisa * 1000; let habis = false;
setInterval(() => {
    const d = Math.max(0, Math.round((deadline - Date.now()) / 1000));
    $('timer').textContent = [Math.floor(d / 3600), Math.floor(d % 3600 / 60), d % 60].map(x => String(x).padStart(2, '0')).join(':');
    $('timer').style.backgroundImage = d <= 300 ? 'linear-gradient(135deg,#ef4444,#f97316)' : '';
    if (d <= 0 && !habis) { habis = true; reloading = true; $('f-selesai').submit(); }
}, 1000);

// ---- Deteksi pindah halaman / tab ----
window.addEventListener('beforeunload', () => { reloading = true; });
document.addEventListener('visibilitychange', async () => {
    if (!document.hidden || reloading || D.maxPelanggaran < 1) return;
    S.hilang = true;
});
document.addEventListener('visibilitychange', async () => {
    if (document.hidden || !S.hilang || reloading) return;
    S.hilang = false;
    const j = await kirim(D.urlPelanggaran);
    if (j && !j.selesai && j.max) tampilModal(`Peringatan ${j.n} dari ${j.max}: Anda meninggalkan halaman ujian. Pada peringatan ke-${j.max}, ujian akan otomatis dikumpulkan.`);
});

$('btn-refresh').onclick = () => { reloading = true; location.reload(); };
$('btn-selesai').onclick = () => {
    const kosong = D.soal.filter(s => !terjawab(s.id)).length, ragu = S.ragu.size;
    let m = 'Kumpulkan jawaban sekarang?';
    if (kosong) m += `\n• ${kosong} soal belum dijawab.`;
    if (ragu) m += `\n• ${ragu} soal masih ditandai ragu-ragu — hilangkan tanda sebelum menyelesaikan bila sudah yakin.`;
    if (confirm(m)) { reloading = true; $('f-selesai').submit(); }
};
document.addEventListener('contextmenu', e => e.preventDefault());
$('judul').textContent = D.judul;
render();
</script>
</body>
</html>
