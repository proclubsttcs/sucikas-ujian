<?php

namespace Database\Seeders;

use App\Models\{BankSoal, Setting, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'guru', 'pengawas', 'siswa'] as $r) Role::findOrCreate($r, 'web');

        $admin = new User(['name' => 'Administrator', 'username' => 'admin', 'nip_nis' => '-']);
        $admin->setPasswordBaru('admin123'); $admin->save(); $admin->assignRole('admin');

        foreach ([
            'nama_sekolah' => 'SMP Contoh Nusantara', 'kota' => 'Tasikmalaya', 'alamat' => 'Jl. Pendidikan No. 1',
            'kepala_sekolah' => 'Drs. Contoh, M.Pd.', 'nip_kepsek' => '197001012000031001', 'tahun_ajaran' => '2026/2027',
            'token' => strtoupper(Str::random(6)), 'tampil_hasil' => '0', 'absensi_sistem' => '1',
        ] as $k => $v) Setting::put($k, $v);

        // ----- Data contoh (hapus bila tidak perlu) -----
        $s = new User(['name' => 'Ahmad Fauzi', 'username' => '1001', 'nip_nis' => '1001', 'kelas' => '7', 'rombel' => 'A', 'jurusan' => 'UMUM', 'agama' => 'Islam']);
        $s->setPasswordBaru('siswa123'); $s->save(); $s->assignRole('siswa');

        $bank = BankSoal::create([
            'user_id' => $admin->id, 'kode' => 'PTS-MTK-7', 'jenis_ujian' => 'PTS', 'mapel' => 'Matematika', 'kelas' => '7',
            'durasi' => 30, 'auto_submit' => 3, 'acak' => true, 'jumlah_opsi' => 4, 'aktif' => true,
            'bobot' => ['pg' => 20, 'pgk' => 20, 'jodoh' => 20, 'bs' => 20, 'uraian' => 20],
        ]);
        $bank->soals()->createMany([
            ['nomor' => 1, 'tipe' => 'pg', 'pertanyaan' => 'Hasil dari 7 × 8 adalah ...', 'kunci' => 'C',
                'opsi' => ['A' => ['t' => '54', 'g' => null], 'B' => ['t' => '48', 'g' => null], 'C' => ['t' => '56', 'g' => null], 'D' => ['t' => '64', 'g' => null]]],
            ['nomor' => 2, 'tipe' => 'pgk', 'pertanyaan' => 'Pilih semua bilangan prima.', 'kunci' => ['A', 'C'],
                'opsi' => ['A' => ['t' => '2', 'g' => null], 'B' => ['t' => '9', 'g' => null], 'C' => ['t' => '7', 'g' => null], 'D' => ['t' => '15', 'g' => null]]],
            ['nomor' => 3, 'tipe' => 'jodoh', 'pertanyaan' => 'Jodohkan bangun datar dengan jumlah sisinya.',
                'opsi' => [['kiri' => 'Segitiga', 'kanan' => '3'], ['kiri' => 'Persegi', 'kanan' => '4'], ['kiri' => 'Segilima', 'kanan' => '5']]],
            ['nomor' => 4, 'tipe' => 'bs', 'pertanyaan' => 'Bilangan 0 adalah bilangan bulat positif.', 'kunci' => 'salah'],
            ['nomor' => 5, 'tipe' => 'uraian', 'pertanyaan' => 'Jelaskan cara menentukan KPK dari 6 dan 8.'],
        ]);
    }
}
