<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('username')->unique();      // siswa: NIS, guru/pengawas/admin: nama
            $t->string('password');
            $t->string('password_kartu')->nullable(); // terenkripsi, hanya untuk cetak kartu peserta
            $t->string('nip_nis')->nullable();
            $t->string('kelas')->nullable();
            $t->string('rombel')->nullable();
            $t->string('jurusan')->nullable();
            $t->string('agama')->nullable();
            $t->string('foto')->nullable();
            $t->rememberToken();
            $t->timestamps();
        });

        Schema::create('sessions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->foreignId('user_id')->nullable()->index();
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->longText('payload');
            $t->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
