<?php

use App\Http\Controllers\{AuthController, BankSoalController, DashboardController, DokumenController, GuruController,
    HasilController, MonitorController, SettingController, SiswaController, SistemController, UjianSiswaController};
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'form'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:30,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/home', [AuthController::class, 'home'])->name('home');

    // ================= ADMIN + GURU =================
    Route::middleware('role:admin|guru')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('dashboard/token', [DashboardController::class, 'token'])->name('token');

        // Bank soal & soal
        Route::resource('bank', BankSoalController::class)->except(['create', 'edit']);
        Route::post('bank/{bank}/bobot', [BankSoalController::class, 'bobot'])->name('bank.bobot');
        Route::post('bank/{bank}/aktif', [BankSoalController::class, 'aktif'])->name('bank.aktif');
        Route::get('bank/{bank}/soal/create', [BankSoalController::class, 'soalCreate'])->name('soal.create');
        Route::post('bank/{bank}/soal', [BankSoalController::class, 'soalStore'])->name('soal.store');
        Route::get('soal/{soal}/edit', [BankSoalController::class, 'soalEdit'])->name('soal.edit');
        Route::put('soal/{soal}', [BankSoalController::class, 'soalUpdate'])->name('soal.update');
        Route::delete('soal/{soal}', [BankSoalController::class, 'soalDestroy'])->name('soal.destroy');
        Route::get('template-soal', [BankSoalController::class, 'template'])->name('soal.template');
        Route::post('bank/{bank}/import', [BankSoalController::class, 'import'])->name('soal.import');
        Route::get('bank/{bank}/files', [BankSoalController::class, 'files'])->name('bank.files');
        Route::post('bank/{bank}/files', [BankSoalController::class, 'upload'])->name('bank.upload');
        Route::delete('bank/{bank}/files', [BankSoalController::class, 'fileDestroy'])->name('bank.filedel');

        // Kartu peserta, daftar hadir, berita acara
        Route::get('dokumen', [DokumenController::class, 'index'])->name('dokumen');
        Route::get('dokumen/kartu', [DokumenController::class, 'kartu'])->name('dokumen.kartu');
        Route::get('dokumen/hadir', [DokumenController::class, 'hadir'])->name('dokumen.hadir');
        Route::get('dokumen/berita', [DokumenController::class, 'berita'])->name('dokumen.berita');
        Route::post('dokumen/absensi', [DokumenController::class, 'absensi'])->name('dokumen.absensi');

        // Hasil & analisa
        Route::get('hasil', [HasilController::class, 'index'])->name('hasil');
        Route::post('hasil/tampil', [HasilController::class, 'tampil'])->name('hasil.tampil');
        Route::get('hasil/ujian/{ujian}', [HasilController::class, 'detail'])->name('hasil.detail');
        Route::get('hasil/ujian/{ujian}/pdf', [HasilController::class, 'pdf'])->name('hasil.pdf');
        Route::post('hasil/ujian/{ujian}/uraian', [HasilController::class, 'uraian'])->name('hasil.uraian');
        Route::delete('hasil/ujian/{ujian}', [HasilController::class, 'reset'])->name('hasil.reset');
        Route::get('hasil/{bank}', [HasilController::class, 'bank'])->name('hasil.bank');
        Route::get('hasil/{bank}/export', [HasilController::class, 'export'])->name('hasil.export');
        Route::post('hasil/{bank}/koreksi-ulang', [HasilController::class, 'koreksiUlang'])->name('hasil.koreksi');
        Route::get('hasil/{bank}/analisa', [HasilController::class, 'analisa'])->name('hasil.analisa');
    });

    // ================= ADMIN SAJA =================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('guru', GuruController::class)->except(['show']);

        Route::get('siswa-template', [SiswaController::class, 'template'])->name('siswa.template');
        Route::post('siswa-import', [SiswaController::class, 'import'])->name('siswa.import');
        Route::get('siswa-foto', [SiswaController::class, 'fotos'])->name('siswa.foto');
        Route::post('siswa-foto', [SiswaController::class, 'uploadFoto'])->name('siswa.foto.upload');
        Route::delete('siswa-foto', [SiswaController::class, 'hapusFoto'])->name('siswa.foto.hapus');
        Route::resource('siswa', SiswaController::class)->except(['show']);

        Route::get('pengaturan', [SettingController::class, 'edit'])->name('pengaturan');
        Route::post('pengaturan', [SettingController::class, 'update'])->name('pengaturan.update');
        Route::get('sistem', [SistemController::class, 'index'])->name('sistem');
        Route::post('sistem/update', [SistemController::class, 'update'])->name('sistem.update');
        Route::get('sistem/backup', [SistemController::class, 'backup'])->name('sistem.backup');
    });

    // ================= MONITOR (ADMIN + PENGAWAS) =================
    Route::middleware('role:admin|pengawas')->prefix('admin')->name('admin.')->group(function () {
        Route::get('monitor', [MonitorController::class, 'index'])->name('monitor');
        Route::post('monitor/{ujian}/selesai', [MonitorController::class, 'selesai'])->name('monitor.selesai');
        Route::post('monitor/{ujian}/reset', [MonitorController::class, 'reset'])->name('monitor.reset');
    });

    // ================= SISWA =================
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('token', [UjianSiswaController::class, 'tokenForm'])->name('token');
        Route::post('token', [UjianSiswaController::class, 'tokenCheck']);
        Route::get('daftar', [UjianSiswaController::class, 'daftar'])->name('daftar');
        Route::get('konfirmasi/{bank}', [UjianSiswaController::class, 'konfirmasi'])->name('konfirmasi');
        Route::post('mulai/{bank}', [UjianSiswaController::class, 'mulai'])->name('mulai');
        Route::get('ujian/{ujian}', [UjianSiswaController::class, 'kerjakan'])->name('ujian');
        Route::post('ujian/{ujian}/jawab', [UjianSiswaController::class, 'jawab'])->name('jawab');
        Route::post('ujian/{ujian}/pelanggaran', [UjianSiswaController::class, 'pelanggaran'])->name('pelanggaran');
        Route::post('ujian/{ujian}/selesai', [UjianSiswaController::class, 'selesai'])->name('selesai');
        Route::get('ujian/{ujian}/hasil', [UjianSiswaController::class, 'hasil'])->name('hasil');
    });
});
