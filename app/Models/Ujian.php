<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    protected $guarded = [];
    protected $casts = [
        'jawaban' => 'array', 'ragu' => 'array', 'urutan' => 'array', 'skor_uraian' => 'array',
        'mulai_at' => 'datetime', 'selesai_at' => 'datetime', 'curang' => 'boolean',
    ];

    public function bank() { return $this->belongsTo(BankSoal::class, 'bank_soal_id'); }
    public function user() { return $this->belongsTo(User::class); }

    public function sisaDetik(): int
    {
        $akhir = $this->mulai_at->copy()->addMinutes($this->bank->durasi);
        return max(0, (int) now()->diffInSeconds($akhir, false));
    }

    public function jumlahTerjawab(): int
    {
        return collect($this->jawaban ?? [])->filter(fn ($v) => $v !== null && $v !== '' && $v !== [])->count();
    }

    /** Hitung ulang seluruh nilai berdasarkan kunci terbaru. */
    public function hitung(): self
    {
        $bank = $this->bank()->first();
        $soals = $bank->soals()->get();
        $max = 0; $auto = 0; $ur = 0; $benar = 0; $salah = 0; $jmlUraian = 0;
        $jawab = $this->jawaban ?? []; $skor = $this->skor_uraian ?? [];

        foreach ($soals as $s) {
            $b = $bank->bobotFor($s->tipe);
            $max += $b;
            if ($s->tipe === 'uraian') {
                $jmlUraian++;
                $ur += min($b, max(0, (float) ($skor[$s->id] ?? 0)));
                continue;
            }
            $j = $jawab[$s->id] ?? null;
            if ($j === null || $j === '' || $j === []) { $salah++; continue; }
            if ($s->benar($j)) { $auto += $b; $benar++; } else { $salah++; }
        }

        $this->total_soal = $soals->count();
        $this->jml_uraian = $jmlUraian;
        $this->jml_benar = $benar;
        $this->jml_salah = $salah;
        $this->nilai_otomatis = $max ? round($auto / $max * 100, 2) : 0;
        $this->nilai_uraian = $max ? round($ur / $max * 100, 2) : 0;
        $this->nilai = $this->nilai_otomatis + $this->nilai_uraian;
        $this->save();
        return $this;
    }

    public function selesaikan(bool $curang = false): self
    {
        if ($this->status === 'selesai') return $this;
        $this->status = 'selesai';
        $this->selesai_at = now();
        if ($curang) $this->curang = true;
        return $this->hitung();
    }
}
