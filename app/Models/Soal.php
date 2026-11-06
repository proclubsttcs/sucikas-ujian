<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Soal extends Model
{
    protected $guarded = [];
    protected $casts = ['opsi' => 'array', 'kunci' => 'array'];

    public function bank() { return $this->belongsTo(BankSoal::class, 'bank_soal_id'); }

    public function url(?string $file): ?string
    {
        return $file ? Storage::url("soal/{$this->bank_soal_id}/".$file) : null;
    }

    /** true = benar, false = salah, null = tidak dinilai otomatis (uraian). */
    public function benar($jwb): ?bool
    {
        switch ($this->tipe) {
            case 'pg':
                return is_string($jwb) && strtoupper($jwb) === strtoupper((string) $this->kunci);
            case 'pgk':
                $a = array_values((array) $jwb); $k = array_values((array) $this->kunci);
                sort($a); sort($k);
                return !empty($a) && $a == $k;
            case 'bs':
                return is_string($jwb) && strtolower($jwb) === strtolower((string) $this->kunci);
            case 'jodoh':
                $j = (array) $jwb;
                foreach ((array) $this->opsi as $i => $p) {
                    if (($j[$i] ?? null) !== $p['kanan']) return false;
                }
                return true;
        }
        return null;
    }

    public function kunciTeks(): string
    {
        return match ($this->tipe) {
            'pg', 'bs' => (string) $this->kunci,
            'pgk' => implode(', ', (array) $this->kunci),
            'jodoh' => collect($this->opsi)->map(fn ($p) => $p['kiri'].' → '.$p['kanan'])->implode('; '),
            default => '-',
        };
    }

    public function jawabanTeks($jwb): string
    {
        if ($jwb === null || $jwb === '' || $jwb === []) return '(tidak dijawab)';
        return match ($this->tipe) {
            'pgk' => implode(', ', (array) $jwb),
            'jodoh' => collect((array) $this->opsi)->map(fn ($p, $i) => $p['kiri'].' → '.(((array) $jwb)[$i] ?? '?'))->implode('; '),
            default => (string) $jwb,
        };
    }
}
