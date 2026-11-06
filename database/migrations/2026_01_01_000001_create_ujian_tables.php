<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->text('value')->nullable();
        });

        Schema::create('bank_soals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('kode');
            $t->string('jenis_ujian');
            $t->string('mapel');
            $t->string('kelas');
            $t->string('jurusan')->nullable();   // null = universal
            $t->string('rombel')->nullable();    // null = semua rombel
            $t->unsignedInteger('durasi')->default(60); // menit
            $t->unsignedTinyInteger('auto_submit')->default(0); // 0 = nonaktif
            $t->boolean('acak')->default(false);
            $t->unsignedTinyInteger('jumlah_opsi')->default(4);
            $t->json('bobot')->nullable();
            $t->boolean('aktif')->default(false);
            $t->timestamps();
        });

        Schema::create('soals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('bank_soal_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('nomor');
            $t->string('tipe', 10); // pg, pgk, jodoh, bs, uraian
            $t->longText('pertanyaan');
            $t->string('gambar')->nullable();
            $t->string('audio')->nullable();
            $t->json('opsi')->nullable();
            $t->json('kunci')->nullable();
            $t->timestamps();
        });

        Schema::create('ujians', function (Blueprint $t) {
            $t->id();
            $t->foreignId('bank_soal_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('status', 15)->default('mengerjakan'); // mengerjakan | selesai
            $t->timestamp('mulai_at')->nullable();
            $t->timestamp('selesai_at')->nullable();
            $t->unsignedTinyInteger('pelanggaran')->default(0);
            $t->boolean('curang')->default(false);
            $t->json('urutan')->nullable();
            $t->json('jawaban')->nullable();
            $t->json('ragu')->nullable();
            $t->json('skor_uraian')->nullable();
            $t->unsignedInteger('total_soal')->default(0);
            $t->unsignedInteger('jml_uraian')->default(0);
            $t->unsignedInteger('jml_benar')->default(0);
            $t->unsignedInteger('jml_salah')->default(0);
            $t->decimal('nilai_otomatis', 6, 2)->default(0);
            $t->decimal('nilai_uraian', 6, 2)->default(0);
            $t->decimal('nilai', 6, 2)->default(0);
            $t->timestamps();
            $t->unique(['bank_soal_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujians');
        Schema::dropIfExists('soals');
        Schema::dropIfExists('bank_soals');
        Schema::dropIfExists('settings');
    }
};
