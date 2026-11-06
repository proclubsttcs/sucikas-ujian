<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class SistemController extends Controller
{
    public function index()
    {
        return view('admin.sistem', ['versi' => 'Laravel '.app()->version().' · PHP '.PHP_VERSION]);
    }

    /** Menjalankan migrasi database & membersihkan cache setelah kode aplikasi diperbarui. */
    public function update()
    {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('optimize:clear');
        return back()->with('ok', 'Pembaruan selesai: migrasi dijalankan dan cache dibersihkan.');
    }

    public function backup()
    {
        $c = config('database.connections.'.config('database.default'));
        abort_unless(in_array($c['driver'], ['mysql', 'mariadb']), 400, 'Backup hanya mendukung MySQL/MariaDB.');
        $p = new Process(['mysqldump', '-h', $c['host'], '-P', (string) $c['port'], '-u', $c['username'], '--single-transaction', $c['database']],
            null, ['MYSQL_PWD' => (string) $c['password']]);
        $p->setTimeout(300);
        $p->run();
        if (!$p->isSuccessful()) return back()->with('err', 'Backup gagal: pastikan mysqldump terpasang. '.substr($p->getErrorOutput(), 0, 200));
        $name = 'backup-'.$c['database'].'-'.now()->format('Ymd-His').'.sql';
        return response($p->getOutput(), 200, ['Content-Type' => 'application/sql', 'Content-Disposition' => "attachment; filename=\"$name\""]);
    }
}
