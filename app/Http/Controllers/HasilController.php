<?php

namespace App\Http\Controllers;

use App\Exports\ArrayExport;
use App\Models\{BankSoal, Setting, Ujian};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HasilController extends Controller
{
    public function index()
    {
        $banks = BankSoal::withCount(['ujians as selesai_count' => fn ($q) => $q->where('status', 'selesai')])
            ->having('selesai_count', '>', 0)->orderBy('mapel')->get();
        return view('admin.hasil.index', ['banks' => $banks, 'tampil' => Setting::get('tampil_hasil') === '1']);
    }

    public function tampil()
    {
        Setting::put('tampil_hasil', Setting::get('tampil_hasil') === '1' ? '0' : '1');
        return back()->with('ok', 'Pengaturan tampil hasil untuk siswa diperbarui.');
    }

    public function bank(BankSoal $bank)
    {
        $rows = Ujian::with('user')->where('bank_soal_id', $bank->id)->where('status', 'selesai')->get()->sortBy('user.name');
        $nilai = $rows->pluck('nilai')->map(fn ($v) => (float) $v);
        $dist = collect(range(0, 9))->map(fn ($i) => $nilai->filter(fn ($v) => $i === 9 ? $v >= 90 : ($v >= $i * 10 && $v < ($i + 1) * 10))->count());
        $stat = ['jumlah' => $rows->count(), 'rata' => round($nilai->avg() ?? 0, 2), 'max' => $nilai->max() ?? 0, 'min' => $nilai->min() ?? 0];
        return view('admin.hasil.bank', compact('bank', 'rows', 'stat', 'dist'));
    }

    public function detail(Ujian $ujian)
    {
        $ujian->load('user', 'bank');
        $soals = $ujian->bank->soals()->get()->keyBy('id');
        $urut = collect($ujian->urutan ?? $soals->keys())->map(fn ($id) => $soals[$id] ?? null)->filter()->sortBy('nomor');
        return view('admin.hasil.detail', ['u' => $ujian, 'soals' => $urut]);
    }

    public function pdf(Ujian $ujian)
    {
        $ujian->load('user', 'bank');
        $soals = $ujian->bank->soals()->get();
        return Pdf::loadView('admin.hasil.pdf', ['u' => $ujian, 'soals' => $soals, 'logo' => Setting::img('logo_sekolah')])
            ->download('hasil-'.$ujian->user->username.'-'.$ujian->bank->kode.'.pdf');
    }

    public function uraian(Request $r, Ujian $ujian)
    {
        $bank = $ujian->bank;
        $skor = $ujian->skor_uraian ?? [];
        foreach ($bank->soals()->where('tipe', 'uraian')->get() as $s) {
            if ($r->has("skor.{$s->id}")) $skor[$s->id] = min($bank->bobotFor('uraian'), max(0, (float) $r->input("skor.{$s->id}")));
        }
        $ujian->skor_uraian = $skor;
        $ujian->hitung();
        return back()->with('ok', 'Nilai uraian disimpan dan diakumulasikan.');
    }

    public function reset(Ujian $ujian)
    {
        $ujian->delete();
        return back()->with('ok', 'Hasil ujian siswa dihapus. Siswa dapat mengulang ujian.');
    }

    public function koreksiUlang(BankSoal $bank)
    {
        $n = 0;
        foreach (Ujian::where('bank_soal_id', $bank->id)->where('status', 'selesai')->get() as $u) { $u->hitung(); $n++; }
        return back()->with('ok', "Koreksi ulang selesai untuk $n siswa berdasarkan kunci jawaban terbaru.");
    }

    public function export(Request $r, BankSoal $bank)
    {
        $rows = Ujian::with('user')->where('bank_soal_id', $bank->id)->where('status', 'selesai')->get()->sortBy('user.name')
            ->map(fn ($u, $i) => [$u->user->name, $u->user->nip_nis, $u->user->kelas.' '.$u->user->rombel, $u->selesai_at?->format('d/m/Y H:i'),
                $bank->mapel, $u->total_soal, $u->jml_uraian, $u->jml_benar, $u->jml_salah, (float) $u->nilai, $u->curang ? 'Ya' : 'Tidak'])->values()->all();
        $head = ['Nama', 'NIS', 'Kelas/Rombel', 'Tanggal Ujian', 'Mata Pelajaran', 'Total Soal', 'Soal Uraian', 'Benar', 'Salah', 'Nilai', 'Terindikasi Curang'];
        $ext = $r->query('format') === 'csv' ? 'csv' : 'xlsx';
        return Excel::download(new ArrayExport($rows, $head), "hasil-{$bank->kode}.$ext");
    }

    public function analisa(BankSoal $bank)
    {
        $ujians = Ujian::where('bank_soal_id', $bank->id)->where('status', 'selesai')->get();
        $n = $ujians->count();
        $rows = $bank->soals()->get()->map(function ($s) use ($ujians, $n) {
            $b = 0; $sl = 0; $k = 0;
            foreach ($ujians as $u) {
                $j = ($u->jawaban ?? [])[$s->id] ?? null;
                if ($j === null || $j === '' || $j === []) { $k++; continue; }
                if ($s->tipe === 'uraian') continue;
                $s->benar($j) ? $b++ : $sl++;
            }
            return ['s' => $s, 'benar' => $b, 'salah' => $sl, 'kosong' => $k, 'persen' => ($n && $s->tipe !== 'uraian') ? round($b / $n * 100, 1) : null];
        });
        return view('admin.hasil.analisa', compact('bank', 'rows', 'n'));
    }
}
