<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    protected $guarded = [];
    protected $casts = ['bobot' => 'array', 'acak' => 'boolean', 'aktif' => 'boolean'];

    public const TIPE = [
        'pg' => 'Pilihan Ganda',
        'pgk' => 'Pilihan Ganda Kompleks',
        'jodoh' => 'Menjodohkan',
        'bs' => 'Benar / Salah',
        'uraian' => 'Uraian',
    ];

    public function soals() { return $this->hasMany(Soal::class)->orderBy('nomor'); }
    public function ujians() { return $this->hasMany(Ujian::class); }

    public function bobotFor(string $tipe): float
    {
        return (float) (($this->bobot ?? [])[$tipe] ?? 1);
    }

    /** Siswa yang berhak mengikuti bank soal ini. */
    public function berlakuUntuk(User $u): bool
    {
        return $this->aktif
            && (string) $this->kelas === (string) $u->kelas
            && (!$this->jurusan || strcasecmp($this->jurusan, (string) $u->jurusan) === 0)
            && (!$this->rombel || strcasecmp($this->rombel, (string) $u->rombel) === 0);
    }

    public function scopeUntukSiswa($q, User $u)
    {
        return $q->where('aktif', true)->where('kelas', $u->kelas)
            ->where(fn ($w) => $w->whereNull('jurusan')->orWhere('jurusan', '')->orWhere('jurusan', $u->jurusan))
            ->where(fn ($w) => $w->whereNull('rombel')->orWhere('rombel', '')->orWhere('rombel', $u->rombel));
    }
}
