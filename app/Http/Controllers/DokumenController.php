<?php

namespace App\Http\Controllers;

use App\Models\{BankSoal, Setting, Ujian, User};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    private function siswaKelas(Request $r)
    {
        return User::siswa()->where('kelas', $r->kelas)
            ->when($r->rombel, fn ($q, $v) => $q->where('rombel', $v))->orderBy('name')->get();
    }

    public function index(Request $r)
    {
        $kelas = User::siswa()->select('kelas', 'rombel')->distinct()->orderBy('kelas')->orderBy('rombel')->get();
        $banks = BankSoal::orderBy('mapel')->get();
        return view('admin.dokumen.index', [
            'kelas' => $kelas, 'banks' => $banks, 'tab' => $r->query('tab', 'kartu'),
            'absensi' => Setting::get('absensi_sistem') === '1', 'hariIni' => now()->toDateString(),
        ]);
    }

    public function absensi()
    {
        Setting::put('absensi_sistem', Setting::get('absensi_sistem') === '1' ? '0' : '1');
        return back()->with('ok', 'Absensi sistem diperbarui.');
    }

    public function kartu(Request $r)
    {
        $r->validate(['kelas' => 'required']);
        $siswa = $this->siswaKelas($r);
        abort_if($siswa->isEmpty(), 404);
        $s = [];
        foreach (['nama_sekolah', 'tahun_ajaran', 'kota', 'kepala_sekolah', 'nip_kepsek'] as $k) $s[$k] = Setting::get($k, '');
        return Pdf::loadView('admin.dokumen.kartu', ['siswa' => $siswa, 'cfg' => $s, 'logo' => Setting::img('logo_sekolah'),
            'ttd' => Setting::img('tanda_tangan'), 'token' => Setting::get('token')])
            ->setPaper('a4')->stream('kartu-peserta-'.$r->kelas.$r->rombel.'.pdf');
    }

    private function konteks(Request $r): array
    {
        $r->validate(['kelas' => 'required', 'bank' => 'required|exists:bank_soals,id', 'tanggal' => 'required|date']);
        $bank = BankSoal::findOrFail($r->bank);
        $siswa = $this->siswaKelas($r);
        $uj = Ujian::where('bank_soal_id', $bank->id)->whereDate('mulai_at', $r->tanggal)->get()->keyBy('user_id');
        $s = [];
        foreach (['nama_sekolah', 'tahun_ajaran', 'kota', 'alamat', 'kepala_sekolah', 'nip_kepsek'] as $k) $s[$k] = Setting::get($k, '');
        return ['bank' => $bank, 'siswa' => $siswa, 'uj' => $uj, 's' => $s, 'tanggal' => \Carbon\Carbon::parse($r->tanggal),
            'kelas' => $r->kelas, 'rombel' => $r->rombel, 'otomatis' => Setting::get('absensi_sistem') === '1',
            'logo' => Setting::url('logo_sekolah'), 'ttd' => Setting::url('tanda_tangan')];
    }

    public function hadir(Request $r) { return view('admin.dokumen.hadir', $this->konteks($r)); }

    public function berita(Request $r)
    {
        $k = $this->konteks($r);
        $k['hadir'] = $k['siswa']->filter(fn ($u) => $k['uj']->has($u->id))->count();
        $k['mulai'] = $k['uj']->min('mulai_at');
        $k['selesai'] = $k['uj']->max('selesai_at');
        if ($r->boolean('doc')) {
            return response(view('admin.dokumen.berita_doc', $k)->render())
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename="berita-acara-'.$k['bank']->kode.'.doc"');
        }
        return view('admin.dokumen.berita', $k);
    }
}
