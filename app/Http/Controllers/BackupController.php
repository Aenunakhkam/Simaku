<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function index()
    {
        return Inertia::render('Backup');
    }

    public function download()
    {
        $dbName = env('DB_DATABASE', 'simaku');
        $dbUser = env('DB_USERNAME', 'postgres');
        $dbPass = env('DB_PASSWORD', 'root');
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '5432');

        $fileName = 'backup_' . $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';
        $backupPath = storage_path('app/' . $fileName);

        // Cari pg_dump dari environment portable SIMAKU
        $pgDumpPath = realpath(base_path('../pgsql/bin/pg_dump.exe'));
        if (!$pgDumpPath) {
            $pgDumpPath = 'pg_dump'; // fallback jika tidak ketemu
        }

        putenv("PGPASSWORD=" . $dbPass);
        $command = "\"$pgDumpPath\" -U $dbUser -h $dbHost -p $dbPort $dbName > \"$backupPath\"";
        
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return back()->with('error', 'Gagal melakukan backup database. Pastikan server PostgreSQL berjalan.');
        }

        return response()->download($backupPath)->deleteFileAfterSend(true);
    }
}
