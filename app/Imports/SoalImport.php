<?php

namespace App\Imports;

use App\Models\BankSoal;
use App\Models\Soal;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SoalImport implements ToCollection, WithHeadingRow
{
    public int $ok = 0;
    public array $gagal = [];

    public function __construct(private BankSoal $bank) {}

    private function s($v): string
    {
        if (is_float($v) || is_int($v)) return rtrim(rtrim(number_format($v, 4, '.', ''), '0'), '.');
        return trim((string) $v);
    }

    private function tipe(string $t): ?string
    {
        $t = strtolower(str_replace([' ', '_', '-', '/'], '', $t));
        return match (true) {
            in_array($t, ['pg', 'pilihanganda']) => 'pg',
            in_array($t, ['pgk', 'pilihangandakompleks', 'kompleks']) => 'pgk',
            in_array($t, ['jodoh', 'menjodohkan']) => 'jodoh',
            in_array($t, ['bs', 'benarsalah', 'benaratausalah']) => 'bs',
            in_array($t, ['uraian', 'essay', 'esai']) => 'uraian',
            default => null,
        };
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $i => $r) {
            $baris = $i + 2;
            $tipe = $this->tipe($this->s($r['jenis'] ?? ''));
            $q = $this->s($r['soal'] ?? '');
            if ($q === '' && $tipe === null) continue;
            if (!$tipe) { $this->gagal[] = "Baris $baris: jenis soal tidak dikenali"; continue; }
            if ($q === '') { $this->gagal[] = "Baris $baris: pertanyaan kosong"; continue; }

            $opsi = null; $kunci = null;
            $kolom = array_slice(['a', 'b', 'c', 'd', 'e'], 0, $this->bank->jumlah_opsi);

            if (in_array($tipe, ['pg', 'pgk'])) {
                $opsi = [];
                foreach ($kolom as $k) {
                    $t = $this->s($r["opsi_$k"] ?? ''); $g = $this->s($r["gambar_opsi_$k"] ?? '');
                    if ($t !== '' || $g !== '') $opsi[strtoupper($k)] = ['t' => $t, 'g' => $g ?: null];
                }
                $raw = strtoupper(preg_replace('/[^A-Za-z,;]/', '', $this->s($r['kunci'] ?? '')));
                $list = array_values(array_filter(preg_split('/[,;]/', $raw) ?: []));
                if (count($opsi) < 2 || !$list) { $this->gagal[] = "Baris $baris: opsi/kunci tidak lengkap"; continue; }
                $kunci = $tipe === 'pg' ? $list[0] : $list;
            } elseif ($tipe === 'jodoh') {
                $opsi = [];
                foreach (['a', 'b', 'c', 'd', 'e'] as $k) {
                    $p = explode('|', $this->s($r["opsi_$k"] ?? ''), 2);
                    if (count($p) === 2 && trim($p[0]) !== '' && trim($p[1]) !== '') $opsi[] = ['kiri' => trim($p[0]), 'kanan' => trim($p[1])];
                }
                if (count($opsi) < 2) { $this->gagal[] = "Baris $baris: menjodohkan butuh min. 2 pasangan (format kiri|kanan)"; continue; }
            } elseif ($tipe === 'bs') {
                $k = strtolower($this->s($r['kunci'] ?? ''));
                $kunci = in_array($k, ['b', 'benar', 'true', '1']) ? 'benar' : (in_array($k, ['s', 'salah', 'false', '0']) ? 'salah' : null);
                if (!$kunci) { $this->gagal[] = "Baris $baris: kunci harus benar/salah"; continue; }
            }

            $nomor = (int) ($r['nomor'] ?? 0) ?: ($this->bank->soals()->max('nomor') + 1);
            Soal::updateOrCreate(
                ['bank_soal_id' => $this->bank->id, 'nomor' => $nomor],
                ['tipe' => $tipe, 'pertanyaan' => $q, 'gambar' => $this->s($r['gambar_soal'] ?? '') ?: null,
                 'audio' => $this->s($r['audio_soal'] ?? '') ?: null, 'opsi' => $opsi, 'kunci' => $kunci]
            );
            $this->ok++;
        }
    }
}
