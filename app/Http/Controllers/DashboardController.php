<?php

namespace App\Http\Controllers;

use App\Models\{BankSoal, Setting, Soal, Ujian, User};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $r)
    {
        if (!Setting::get('token')) Setting::put('token', strtoupper(Str::random(6)));
        $banks = BankSoal::withCount('soals')->where('aktif', true)
            ->when($r->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('mapel', 'like', "%$s%")->orWhere('kode', 'like', "%$s%")))
            ->orderBy('mapel')->get();
        return view('admin.dashboard', [
            'token' => Setting::get('token'),
            'banks' => $banks,
            'jmlSiswaUjian' => Ujian::distinct()->count('user_id'),
            'jmlSiswa' => User::siswa()->count(),
            'jmlBank' => BankSoal::count(),
            'jmlSoalAktif' => Soal::whereHas('bank', fn ($q) => $q->where('aktif', true))->count(),
        ]);
    }

    public function token()
    {
        Setting::put('token', strtoupper(Str::random(6)));
        return back()->with('ok', 'Token ujian berhasil diperbarui.');
    }
}
