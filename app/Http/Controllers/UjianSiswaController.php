<?php

namespace App\Http\Controllers;

use App\Models\{BankSoal, Setting, Ujian};
use Illuminate\Http\Request;

class UjianSiswaController extends Controller
{
    private function milik(Ujian $u): void { abort_unless($u->user_id === auth()->id(), 403); }

    public function tokenForm() { return view('siswa.token'); }

    public function tokenCheck(Request $r)
    {
        $r->validate(['token' => 'required|string']);
        if (strcasecmp(trim($r->token), (string) Setting::get('token')) !== 0) {
            return back()->withErrors(['token' => 'Token ujian tidak valid.']);
        }
        session(['token_ok' => true]);
        return redirect()->route('siswa.daftar');
    }

    public function daftar()
    {
        if (!session('token_ok')) return redirect()->route('siswa.token');
        $u = auth()->user();
        $banks = BankSoal::untukSiswa($u)->withCount('soals')->orderBy('mapel')->get();
        $uj = Ujian::where('user_id', $u->id)->get()->keyBy('bank_soal_id');
        return view('siswa.daftar', compact('banks', 'uj'));
    }

    public function konfirmasi(BankSoal $bank)
    {
        abort_unless(session('token_ok') && $bank->berlakuUntuk(auth()->user()), 403);
        $uj = Ujian::where(['user_id' => auth()->id(), 'bank_soal_id' => $bank->id])->first();
        if ($uj?->status === 'selesai') return redirect()->route('siswa.daftar')->with('err', 'Anda sudah menyelesaikan ujian ini.');
        if ($uj) return redirect()->route('siswa.ujian', $uj);
        return view('siswa.konfirmasi', compact('bank'));
    }

    public function mulai(BankSoal $bank)
    {
        $u = auth()->user();
        abort_unless(session('token_ok') && $bank->berlakuUntuk($u), 403);
        $uj = Ujian::where(['user_id' => $u->id, 'bank_soal_id' => $bank->id])->first();
        if ($uj?->status === 'selesai') return redirect()->route('siswa.daftar')->with('err', 'Ujian sudah selesai.');
        if (!$uj) {
            $ids = $bank->soals()->pluck('id')->all();
            if (!$ids) return back()->with('err', 'Bank soal belum memiliki soal.');
            if ($bank->acak) shuffle($ids);
            $uj = Ujian::create([
                'bank_soal_id' => $bank->id, 'user_id' => $u->id, 'status' => 'mengerjakan', 'mulai_at' => now(),
                'urutan' => $ids, 'jawaban' => [], 'ragu' => [], 'total_soal' => count($ids),
            ]);
        }
        return redirect()->route('siswa.ujian', $uj);
    }

    public function kerjakan(Ujian $ujian)
    {
        $this->milik($ujian);
        if ($ujian->status === 'selesai') return redirect()->route('siswa.hasil', $ujian);
        if ($ujian->sisaDetik() <= 0) { $ujian->selesaikan(); return redirect()->route('siswa.hasil', $ujian); }

        $bank = $ujian->bank;
        $soals = $bank->soals()->get()->keyBy('id');
        $items = collect($ujian->urutan)->map(fn ($id) => $soals[$id] ?? null)->filter()->values()->map(function ($s) {
            $it = ['id' => $s->id, 'tipe' => $s->tipe, 'q' => $s->pertanyaan, 'img' => $s->url($s->gambar), 'aud' => $s->url($s->audio)];
            if (in_array($s->tipe, ['pg', 'pgk'])) {
                $it['opsi'] = collect($s->opsi)->map(fn ($o, $k) => ['k' => $k, 't' => $o['t'] ?? '', 'g' => $s->url($o['g'] ?? null)])->values();
            } elseif ($s->tipe === 'jodoh') {
                $it['kiri'] = collect($s->opsi)->pluck('kiri')->values();
                $kanan = collect($s->opsi)->pluck('kanan')->unique()->values()->all();
                mt_srand($s->id); shuffle($kanan); mt_srand();
                $it['kanan'] = $kanan;
            }
            return $it;
        })->values();

        $data = [
            'judul' => $bank->mapel.' — '.$bank->jenis_ujian, 'sisa' => $ujian->sisaDetik(),
            'maxPelanggaran' => (int) $bank->auto_submit, 'pelanggaran' => (int) $ujian->pelanggaran,
            'jawaban' => (object) ($ujian->jawaban ?? []), 'ragu' => array_values($ujian->ragu ?? []), 'soal' => $items,
            'urlJawab' => route('siswa.jawab', $ujian), 'urlPelanggaran' => route('siswa.pelanggaran', $ujian),
            'urlSelesai' => route('siswa.selesai', $ujian),
        ];
        return view('siswa.ujian', compact('data', 'ujian'));
    }

    public function jawab(Request $r, Ujian $ujian)
    {
        $this->milik($ujian);
        if ($ujian->status === 'selesai') return response()->json(['selesai' => true, 'url' => route('siswa.hasil', $ujian)]);
        if ($ujian->sisaDetik() <= -10) { $ujian->selesaikan(); return response()->json(['selesai' => true, 'url' => route('siswa.hasil', $ujian)]); }

        $sid = (int) $r->input('soal_id');
        abort_unless(in_array($sid, $ujian->urutan ?? []), 422);
        $j = $ujian->jawaban ?? [];
        $v = $r->input('jawaban');
        if ($v === null || $v === '' || $v === []) unset($j[$sid]); else $j[$sid] = $v;
        $ragu = collect($ujian->ragu ?? [])->reject(fn ($x) => $x == $sid);
        if ($r->boolean('ragu')) $ragu->push($sid);
        $ujian->update(['jawaban' => $j, 'ragu' => $ragu->values()->all()]);
        return response()->json(['ok' => true, 'terjawab' => $ujian->jumlahTerjawab()]);
    }

    public function pelanggaran(Ujian $ujian)
    {
        $this->milik($ujian);
        $max = (int) $ujian->bank->auto_submit;
        if ($ujian->status === 'selesai') return response()->json(['selesai' => true, 'url' => route('siswa.hasil', $ujian)]);
        if ($max < 1) return response()->json(['n' => 0, 'max' => 0]);
        $ujian->increment('pelanggaran');
        if ($ujian->pelanggaran >= $max) {
            $ujian->selesaikan(true);
            return response()->json(['selesai' => true, 'url' => route('siswa.hasil', $ujian)]);
        }
        return response()->json(['n' => $ujian->pelanggaran, 'max' => $max]);
    }

    public function selesai(Ujian $ujian)
    {
        $this->milik($ujian);
        $ujian->selesaikan();
        return redirect()->route('siswa.hasil', $ujian);
    }

    public function hasil(Ujian $ujian)
    {
        $this->milik($ujian);
        abort_unless($ujian->status === 'selesai', 404);
        return view('siswa.selesai', ['ujian' => $ujian, 'tampil' => Setting::get('tampil_hasil') === '1']);
    }
}
