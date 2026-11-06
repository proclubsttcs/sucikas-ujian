<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public const TEKS = ['nama_sekolah', 'kota', 'alamat', 'kepala_sekolah', 'nip_kepsek', 'tahun_ajaran'];
    public const FILE = ['logo_sekolah', 'logo_kota', 'tanda_tangan'];

    public function edit()
    {
        $s = collect(array_merge(self::TEKS, self::FILE))->mapWithKeys(fn ($k) => [$k => Setting::get($k, '')]);
        return view('admin.pengaturan', ['s' => $s, 'urls' => collect(self::FILE)->mapWithKeys(fn ($k) => [$k => Setting::url($k)])]);
    }

    public function update(Request $r)
    {
        $r->validate(collect(self::TEKS)->mapWithKeys(fn ($k) => [$k => 'nullable|string|max:200'])->all()
            + collect(self::FILE)->mapWithKeys(fn ($k) => [$k => 'nullable|image|max:2048'])->all());
        foreach (self::TEKS as $k) Setting::put($k, $r->input($k));
        foreach (self::FILE as $k) {
            if ($f = $r->file($k)) { $nm = $k.'.'.$f->getClientOriginalExtension(); $f->storeAs('pengaturan', $nm, 'public'); Setting::put($k, $nm); }
        }
        return back()->with('ok', 'Pengaturan diterapkan.');
    }
}
