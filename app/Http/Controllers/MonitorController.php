<?php

namespace App\Http\Controllers;

use App\Models\{BankSoal, Ujian, User};
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function index(Request $r)
    {
        $banks = BankSoal::where('aktif', true)->orderBy('mapel')->get();
        $bank = $r->bank ? $banks->firstWhere('id', (int) $r->bank) : $banks->first();
        $rows = collect(); $uj = collect();
        if ($bank) {
            $uj = Ujian::where('bank_soal_id', $bank->id)->get()->keyBy('user_id');
            $rows = User::siswa()->where('kelas', $bank->kelas)
                ->when($bank->jurusan, fn ($q, $j) => $q->where('jurusan', $j))
                ->when($bank->rombel, fn ($q, $j) => $q->where('rombel', $j))
                ->when($r->q, fn ($q, $s) => $q->where('name', 'like', "%$s%"))
                ->orderBy('name')->get();
        }
        $total = $bank?->soals()->count() ?? 0;
        return view('admin.monitor.index', compact('banks', 'bank', 'rows', 'uj', 'total'));
    }

    public function selesai(Ujian $ujian)
    {
        $ujian->selesaikan();
        return back()->with('ok', 'Ujian siswa diselesaikan.');
    }

    public function reset(Ujian $ujian)
    {
        $ujian->delete();
        return back()->with('ok', 'Ujian siswa direset. Siswa dapat mengerjakan ulang dari awal.');
    }
}
