<?php

namespace App\Http\Controllers;

use App\Exports\ArrayExport;
use App\Imports\SoalImport;
use App\Models\{BankSoal, Soal};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class BankSoalController extends Controller
{
    private function rules(): array
    {
        return [
            'jenis_ujian' => 'required|string|max:60', 'kode' => 'required|string|max:40',
            'mapel' => 'required|string|max:100', 'kelas' => 'required|string|max:20',
            'jurusan' => 'nullable|string|max:50', 'rombel' => 'nullable|string|max:20',
            'durasi' => 'required|integer|min:1|max:600', 'auto_submit' => 'required|integer|min:0|max:10',
            'jumlah_opsi' => 'required|in:4,5', 'acak' => 'nullable|boolean',
        ];
    }

    public function index(Request $r)
    {
        $banks = BankSoal::withCount('soals')
            ->when($r->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('mapel', 'like', "%$s%")->orWhere('kode', 'like', "%$s%")))
            ->latest()->get();
        return view('admin.bank.index', compact('banks'));
    }

    public function store(Request $r)
    {
        $d = $r->validate($this->rules());
        $d['acak'] = $r->boolean('acak');
        $d['user_id'] = auth()->id();
        $d['bobot'] = ['pg' => 2, 'pgk' => 3, 'jodoh' => 3, 'bs' => 2, 'uraian' => 10];
        $bank = BankSoal::create($d);
        return redirect()->route('admin.bank.show', $bank)->with('ok', 'Bank soal ditambahkan. Atur bobot lalu tambahkan soal.');
    }

    public function update(Request $r, BankSoal $bank)
    {
        $d = $r->validate($this->rules());
        $d['acak'] = $r->boolean('acak');
        $bank->update($d);
        return back()->with('ok', 'Bank soal diperbarui.');
    }

    public function destroy(BankSoal $bank)
    {
        Storage::disk('public')->deleteDirectory("soal/{$bank->id}");
        $bank->delete();
        return redirect()->route('admin.bank.index')->with('ok', 'Bank soal dihapus.');
    }

    public function show(BankSoal $bank)
    {
        $soals = $bank->soals()->get();
        return view('admin.bank.detail', compact('bank', 'soals'));
    }

    public function bobot(Request $r, BankSoal $bank)
    {
        $r->validate(['bobot' => 'required|array', 'bobot.*' => 'required|numeric|min:0|max:1000']);
        $bank->update(['bobot' => collect(array_keys(BankSoal::TIPE))->mapWithKeys(fn ($t) => [$t => (float) ($r->input("bobot.$t") ?? 1)])->all()]);
        return back()->with('ok', 'Bobot disimpan.');
    }

    public function aktif(BankSoal $bank)
    {
        if (!$bank->aktif && $bank->soals()->count() === 0) return back()->with('err', 'Bank soal belum memiliki soal.');
        $bank->update(['aktif' => !$bank->aktif]);
        return back()->with('ok', $bank->aktif ? 'Soal diaktifkan dan siap digunakan.' : 'Soal dinonaktifkan.');
    }

    // ---------- Soal ----------
    public function soalCreate(BankSoal $bank)
    {
        return view('admin.bank.soal_form', ['bank' => $bank, 'soal' => new Soal(['nomor' => $bank->soals()->max('nomor') + 1, 'tipe' => 'pg'])]);
    }

    public function soalEdit(Soal $soal)
    {
        return view('admin.bank.soal_form', ['bank' => $soal->bank, 'soal' => $soal]);
    }

    private function payload(Request $r, BankSoal $bank, ?Soal $old = null): array
    {
        $r->validate([
            'tipe' => 'required|in:pg,pgk,jodoh,bs,uraian', 'nomor' => 'required|integer|min:1',
            'pertanyaan' => 'required|string', 'gambar' => 'nullable|image|max:4096',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:10240',
        ]);
        $tipe = $r->tipe;
        $data = ['tipe' => $tipe, 'nomor' => $r->nomor, 'pertanyaan' => $r->pertanyaan, 'opsi' => null, 'kunci' => null];
        $err = fn ($m) => throw ValidationException::withMessages(['opsi' => $m]);

        if (in_array($tipe, ['pg', 'pgk'])) {
            $opsi = [];
            foreach (array_slice(['A', 'B', 'C', 'D', 'E'], 0, $bank->jumlah_opsi) as $L) {
                $t = trim((string) $r->input("opsi.$L"));
                $g = $old?->opsi[$L]['g'] ?? null;
                if ($t !== '' || $g) $opsi[$L] = ['t' => $t, 'g' => $g];
            }
            if (count($opsi) < 2) $err('Isi minimal dua opsi jawaban.');
            $kunci = $tipe === 'pg' ? $r->input('kunci_pg') : array_values((array) $r->input('kunci_pgk', []));
            if (!$kunci || (is_string($kunci) && !isset($opsi[$kunci])) || (is_array($kunci) && array_diff($kunci, array_keys($opsi)))) $err('Kunci jawaban wajib diisi dan harus sesuai opsi.');
            $data['opsi'] = $opsi; $data['kunci'] = $kunci;
        } elseif ($tipe === 'jodoh') {
            $pasang = [];
            foreach (preg_split('/\R/', (string) $r->input('pasangan')) as $line) {
                $p = explode('|', $line, 2);
                if (count($p) === 2 && trim($p[0]) !== '' && trim($p[1]) !== '') $pasang[] = ['kiri' => trim($p[0]), 'kanan' => trim($p[1])];
            }
            if (count($pasang) < 2) $err('Isi minimal dua pasangan dengan format: kiri|kanan (satu per baris).');
            $data['opsi'] = $pasang;
        } elseif ($tipe === 'bs') {
            if (!in_array($r->kunci_bs, ['benar', 'salah'])) $err('Pilih kunci Benar atau Salah.');
            $data['kunci'] = $r->kunci_bs;
        }

        $dir = "soal/{$bank->id}";
        foreach (['gambar', 'audio'] as $f) {
            if ($file = $r->file($f)) { $nm = basename($file->getClientOriginalName()); $file->storeAs($dir, $nm, 'public'); $data[$f] = $nm; }
            elseif ($old) $data[$f] = $r->boolean("hapus_$f") ? null : $old->$f;
        }
        return $data;
    }

    public function soalStore(Request $r, BankSoal $bank)
    {
        $data = $this->payload($r, $bank);
        if ($bank->soals()->where('nomor', $data['nomor'])->exists()) {
            throw ValidationException::withMessages(['nomor' => 'Nomor soal sudah dipakai.']);
        }
        $bank->soals()->create($data);
        return redirect()->route('admin.bank.show', $bank)->with('ok', 'Soal berhasil ditambahkan.');
    }

    public function soalUpdate(Request $r, Soal $soal)
    {
        $data = $this->payload($r, $soal->bank, $soal);
        if ($soal->bank->soals()->where('nomor', $data['nomor'])->where('id', '!=', $soal->id)->exists()) {
            throw ValidationException::withMessages(['nomor' => 'Nomor soal sudah dipakai.']);
        }
        $soal->update($data);
        return redirect()->route('admin.bank.show', $soal->bank)->with('ok', 'Soal diperbarui.');
    }

    public function soalDestroy(Soal $soal)
    {
        $soal->delete();
        return back()->with('ok', 'Soal dihapus.');
    }

    public function template()
    {
        $head = ['jenis', 'nomor', 'soal', 'gambar_soal', 'audio_soal', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e',
            'gambar_opsi_a', 'gambar_opsi_b', 'gambar_opsi_c', 'gambar_opsi_d', 'gambar_opsi_e', 'kunci'];
        $rows = [
            ['pg', 1, 'Ibu kota Indonesia adalah ...', '', '', 'Bandung', 'Jakarta', 'Surabaya', 'Medan', '', '', '', '', '', '', 'B'],
            ['pgk', 2, 'Pilih bilangan prima.', '', '', '2', '4', '5', '9', '', '', '', '', '', '', 'A,C'],
            ['jodoh', 3, 'Jodohkan negara dan ibu kotanya.', '', '', 'Jepang|Tokyo', 'Prancis|Paris', 'Mesir|Kairo', '', '', '', '', '', '', '', ''],
            ['bs', 4, 'Matahari terbit dari barat.', '', '', '', '', '', '', '', '', '', '', '', '', 'salah'],
            ['uraian', 5, 'Jelaskan proses fotosintesis!', '', '', '', '', '', '', '', '', '', '', '', '', ''],
        ];
        return Excel::download(new ArrayExport($rows, $head), 'template-soal.xlsx');
    }

    public function import(Request $r, BankSoal $bank)
    {
        $r->validate(['file' => 'required|file|mimes:xlsx,xls,csv']);
        $imp = new SoalImport($bank);
        Excel::import($imp, $r->file('file'));
        $res = back()->with('ok', "{$imp->ok} soal berhasil diimpor.");
        return $imp->gagal ? $res->with('peringatan', $imp->gagal) : $res;
    }

    // ---------- File gambar/audio ----------
    public function files(BankSoal $bank)
    {
        $files = collect(Storage::disk('public')->files("soal/{$bank->id}"))->map(fn ($f) => basename($f))->sort()->values();
        return view('admin.bank.files', compact('bank', 'files'));
    }

    public function upload(Request $r, BankSoal $bank)
    {
        $r->validate(['files' => 'required|array', 'files.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp3,wav,ogg,m4a|max:10240']);
        foreach ($r->file('files') as $f) $f->storeAs("soal/{$bank->id}", basename($f->getClientOriginalName()), 'public');
        return back()->with('ok', count($r->file('files')).' file diunggah.');
    }

    public function fileDestroy(Request $r, BankSoal $bank)
    {
        foreach ((array) $r->input('files', []) as $f) Storage::disk('public')->delete("soal/{$bank->id}/".basename($f));
        return back()->with('ok', 'File terpilih dihapus.');
    }
}
