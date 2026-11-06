<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToCollection, WithHeadingRow
{
    public int $ok = 0;
    public array $gagal = [];

    private function str($v): string
    {
        if (is_float($v) || is_int($v)) return number_format($v, 0, '', '');
        return trim((string) $v);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $i => $r) {
            $baris = $i + 2;
            $nama = $this->str($r['nama'] ?? '');
            $nis = $this->str($r['nis'] ?? '');
            $kelas = $this->str($r['kelas'] ?? '');
            $pass = $this->str($r['password'] ?? '');
            if ($nama === '' && $nis === '') continue;
            if ($nama === '' || $nis === '' || $kelas === '' || $pass === '') {
                $this->gagal[] = "Baris $baris: nama, NIS, kelas, dan password wajib diisi"; continue;
            }
            if (User::where('username', $nis)->exists()) {
                $this->gagal[] = "Baris $baris: NIS $nis sudah terdaftar"; continue;
            }
            $u = new User([
                'name' => $nama, 'username' => $nis, 'nip_nis' => $nis, 'kelas' => $kelas,
                'rombel' => $this->str($r['rombel'] ?? '') ?: null,
                'jurusan' => $this->str($r['jurusan'] ?? '') ?: null,
                'agama' => $this->str($r['agama'] ?? '') ?: null,
                'foto' => $this->str($r['foto'] ?? '') ?: null,
            ]);
            $u->setPasswordBaru($pass);
            $u->save();
            $u->assignRole('siswa');
            $this->ok++;
        }
    }
}
