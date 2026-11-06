<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $guarded = [];
    protected $hidden = ['password', 'remember_token', 'password_kartu'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function scopeSiswa(Builder $q): Builder
    {
        return $q->role('siswa');
    }

    public function ujians()
    {
        return $this->hasMany(Ujian::class);
    }

    /** Set password + simpan salinan terenkripsi untuk kartu peserta. */
    public function setPasswordBaru(string $plain): void
    {
        $this->password = $plain;
        $this->password_kartu = Crypt::encryptString($plain);
    }

    public function passwordKartu(): string
    {
        try { return $this->password_kartu ? Crypt::decryptString($this->password_kartu) : '-'; }
        catch (\Throwable) { return '-'; }
    }

    public function fotoUrl(): ?string
    {
        return $this->foto && Storage::disk('public')->exists('siswa/'.$this->foto)
            ? Storage::url('siswa/'.$this->foto) : null;
    }
}
