<?php

namespace App\Http\Controllers;

use App\Exports\ArrayExport;
use App\Imports\SiswaImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    public function index(Request $r)
    {
        $siswa = User::siswa()
            ->when($r->kelas, fn ($q, $k) => $q->where('kelas', $k))
            ->when($r->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('nip_nis', 'like', "%$s%")))
            ->orderBy('kelas')->orderBy('rombel')->orderBy('name')->paginate(25)->withQueryString();
        $kelasList = User::siswa()->whereNotNull('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        return view('admin.siswa.index', compact('siswa', 'kelasList'));
    }

    public function create() { return view('admin.siswa.form', ['u' => new User]); }

    private function rules(?User $u = null): array
    {
        return [
            'name' => 'required|string|max:150',
            'nis' => ['required', 'string', 'max:40', Rule::unique('users', 'username')->ignore($u?->id)],
            'kelas' => 'required|string|max:20',
            'rombel' => 'nullable|string|max:20',
            'jurusan' => 'nullable|string|max:50',
            'agama' => 'nullable|string|max:30',
            'password' => [$u ? 'nullable' : 'required', 'string', 'min:4'],
            'foto' => 'nullable|image|max:2048',
        ];
    }

    private function simpan(Request $r, User $u)
    {
        $d = $r->validate($this->rules($u->exists ? $u : null));
        $u->fill([
            'name' => $d['name'], 'username' => $d['nis'], 'nip_nis' => $d['nis'], 'kelas' => $d['kelas'],
            'rombel' => $d['rombel'] ?? null, 'jurusan' => $d['jurusan'] ?? null, 'agama' => $d['agama'] ?? null,
        ]);
        if (!empty($d['password'])) $u->setPasswordBaru($d['password']);
        if ($f = $r->file('foto')) {
            $nama = basename($f->getClientOriginalName());
            $f->storeAs('siswa', $nama, 'public');
            $u->foto = $nama;
        }
        $u->save();
        if (!$u->hasRole('siswa')) $u->assignRole('siswa');
    }

    public function store(Request $r)
    {
        $this->simpan($r, new User);
        return redirect()->route('admin.siswa.index')->with('ok', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(User $siswa) { abort_unless($siswa->hasRole('siswa'), 404); return view('admin.siswa.form', ['u' => $siswa]); }

    public function update(Request $r, User $siswa)
    {
        abort_unless($siswa->hasRole('siswa'), 404);
        $this->simpan($r, $siswa);
        return redirect()->route('admin.siswa.index')->with('ok', 'Data siswa diperbarui.');
    }

    public function destroy(User $siswa)
    {
        abort_unless($siswa->hasRole('siswa'), 404);
        $siswa->delete();
        return back()->with('ok', 'Data siswa dihapus.');
    }

    public function template()
    {
        return Excel::download(new ArrayExport(
            [['Ahmad Fauzi', 'ahmad.jpg', '1001', '7', 'Islam', 'UMUM', 'A', 'rahasia1']],
            ['nama', 'foto', 'nis', 'kelas', 'agama', 'jurusan', 'rombel', 'password']
        ), 'template-siswa.xlsx');
    }

    public function import(Request $r)
    {
        $r->validate(['file' => 'required|file|mimes:xlsx,xls,csv']);
        $imp = new SiswaImport;
        Excel::import($imp, $r->file('file'));
        $r = back()->with('ok', "{$imp->ok} siswa berhasil diimpor.");
        return $imp->gagal ? $r->with('peringatan', $imp->gagal) : $r;
    }

    public function fotos()
    {
        $files = collect(Storage::disk('public')->files('siswa'))->map(fn ($f) => basename($f))->sort()->values();
        return view('admin.siswa.foto', compact('files'));
    }

    public function uploadFoto(Request $r)
    {
        $r->validate(['fotos' => 'required|array', 'fotos.*' => 'image|max:2048']);
        foreach ($r->file('fotos') as $f) $f->storeAs('siswa', basename($f->getClientOriginalName()), 'public');
        return back()->with('ok', count($r->file('fotos')).' foto berhasil diunggah.');
    }

    public function hapusFoto(Request $r)
    {
        foreach ((array) $r->input('files', []) as $f) Storage::disk('public')->delete('siswa/'.basename($f));
        return back()->with('ok', 'Foto terpilih dihapus.');
    }
}
