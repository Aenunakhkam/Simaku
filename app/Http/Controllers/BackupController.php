<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function index()
    {
        // Pastikan folder ada
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $files = \Illuminate\Support\Facades\File::files(storage_path('app/backups'));
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'zip') {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => round($file->getSize() / 1024 / 1024, 2) . ' MB',
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    'timestamp' => $file->getMTime(),
                ];
            }
        }

        // Urutkan dari yang terbaru
        usort($backups, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return Inertia::render('Backup', [
            'backups' => $backups
        ]);
    }

    public function create()
    {
        $dbName = env('DB_DATABASE', 'simaku');
        $dbUser = env('DB_USERNAME', 'postgres');
        $dbPass = env('DB_PASSWORD', 'root');
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '5432');

        // Pastikan folder backups ada
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $sqlFileName = 'backup_' . $dbName . '_' . $timestamp . '.sql';
        $zipFileName = 'backup_' . $dbName . '_' . $timestamp . '.zip';
        
        $sqlPath = storage_path('app/backups/' . $sqlFileName);
        $zipPath = storage_path('app/backups/' . $zipFileName);

        // Cari pg_dump
        $pgDumpPath = realpath(base_path('../pgsql/bin/pg_dump.exe'));
        if (!$pgDumpPath) {
            $pgDumpPath = 'pg_dump'; 
        }

        putenv("PGPASSWORD=" . $dbPass);
        $command = "\"$pgDumpPath\" -U $dbUser -h $dbHost -p $dbPort $dbName > \"$sqlPath\"";
        
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            if (file_exists($sqlPath)) unlink($sqlPath);
            return back()->with('error', 'Gagal membuat backup database.');
        }

        // Buat ZIP menggunakan ZipArchive jika tersedia, jika tidak gunakan tar bawaan Windows
        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                $zip->addFile($sqlPath, $sqlFileName);
                $zip->close();
                unlink($sqlPath);
            } else {
                return back()->with('error', 'Gagal mengompresi backup menjadi ZIP.');
            }
        } else {
            // Fallback menggunakan OS command (Windows tar)
            // Masuk ke direktori backups agar di dalam zip tidak ada struktur folder absolute
            $backupDir = storage_path('app/backups');
            $tarCommand = "cd \"$backupDir\" && tar -a -c -f \"$zipFileName\" \"$sqlFileName\"";
            exec($tarCommand, $tarOutput, $tarReturn);
            if ($tarReturn === 0) {
                unlink($sqlPath);
            } else {
                return back()->with('error', 'Gagal mengompresi backup menjadi ZIP menggunakan tar.');
            }
        }

        return back()->with('success', 'Backup berhasil dibuat.');
    }

    public function download($file)
    {
        $path = storage_path('app/backups/' . $file);
        if (file_exists($path)) {
            return response()->download($path);
        }
        return back()->with('error', 'File tidak ditemukan.');
    }

    public function delete($file)
    {
        $path = storage_path('app/backups/' . $file);
        if (file_exists($path)) {
            unlink($path);
            return back()->with('success', 'File backup berhasil dihapus.');
        }
        return back()->with('error', 'File tidak ditemukan.');
    }

    public function restore(Request $request)
    {
        set_time_limit(300);

        $request->validate([
            'backup_file' => 'required|file|max:50000',
        ]);

        $file = $request->file('backup_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['zip', 'sql'])) {
            return back()->with('error', 'Format file tidak didukung. Harap upload file .zip atau .sql.');
        }

        $tempDir = storage_path('app/backups/temp_restore_' . time());
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $sqlPath = null;

        if ($extension === 'zip') {
            if (class_exists('ZipArchive')) {
                $zip = new \ZipArchive();
                $res = $zip->open($file->getRealPath());
                if ($res === TRUE) {
                    $sqlFileName = null;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $stat = $zip->statIndex($i);
                        if (pathinfo($stat['name'], PATHINFO_EXTENSION) === 'sql') {
                            $sqlFileName = $stat['name'];
                            break;
                        }
                    }
                    if (!$sqlFileName) {
                        $zip->close();
                        \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
                        return back()->with('error', 'File ZIP tidak valid. Tidak ada file .sql ditemukan.');
                    }
                    $zip->extractTo($tempDir, $sqlFileName);
                    $sqlPath = $tempDir . '/' . $sqlFileName;
                    $zip->close();
                } else {
                    \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
                    return back()->with('error', 'Gagal membaca file ZIP.');
                }
            } else {
                // Fallback using Windows tar
                $zipRealPath = $file->getRealPath();
                exec("cd \"$tempDir\" && tar -x -f \"$zipRealPath\"", $tarOutput, $tarRes);
                if ($tarRes === 0) {
                    $files = \Illuminate\Support\Facades\File::files($tempDir);
                    foreach ($files as $f) {
                        if ($f->getExtension() === 'sql') {
                            $sqlPath = $f->getRealPath();
                            break;
                        }
                    }
                }
                if (!$sqlPath) {
                    \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
                    return back()->with('error', 'Gagal mengekstrak file ZIP atau tidak ada file .sql di dalamnya.');
                }
            }
        } else {
            // Directly SQL file
            $sqlFileName = 'restore_' . time() . '.sql';
            $file->move($tempDir, $sqlFileName);
            $sqlPath = $tempDir . '/' . $sqlFileName;
        }

        // Jalankan sanity check isi SQL file
        if (!file_exists($sqlPath)) {
            \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
            return back()->with('error', 'File SQL tidak ditemukan.');
        }

        $sqlContentSample = file_get_contents($sqlPath, false, null, 0, 1000);
        if (strpos($sqlContentSample, 'PostgreSQL database dump') === false && strpos($sqlContentSample, 'CREATE TABLE') === false) {
            \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
            return back()->with('error', 'File backup tidak valid. File harus merupakan PostgreSQL database dump.');
        }

        // Database credentials
        $dbName = env('DB_DATABASE', 'simaku');
        $dbUser = env('DB_USERNAME', 'postgres');
        $dbPass = env('DB_PASSWORD', 'root');
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '5432');

        // Paths to postgres tools
        $pgDumpPath = realpath(base_path('../pgsql/bin/pg_dump.exe'));
        if (!$pgDumpPath) {
            $pgDumpPath = 'pg_dump'; 
        }

        $psqlPath = realpath(base_path('../pgsql/bin/psql.exe'));
        if (!$psqlPath) {
            $psqlPath = 'psql'; 
        }

        // 1. Buat auto backup dari database saat ini sebelum di-overwrite
        $autoBackupFileName = 'auto_backup_before_restore_' . time() . '.sql';
        $autoBackupPath = storage_path('app/backups/' . $autoBackupFileName);
        
        putenv("PGPASSWORD=" . $dbPass);
        $backupCmd = "\"$pgDumpPath\" -U $dbUser -h $dbHost -p $dbPort $dbName > \"$autoBackupPath\"";
        exec($backupCmd, $backupOutput, $backupRes);

        if ($backupRes !== 0) {
            if (file_exists($autoBackupPath)) unlink($autoBackupPath);
            \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
            return back()->with('error', 'Gagal membuat backup otomatis sebelum restore. Restore dibatalkan.');
        }

        // 2. Kosongkan database saat ini (Drop & Create schema public)
        $dropCmd = "\"$psqlPath\" -U $dbUser -h $dbHost -p $dbPort -d $dbName -c \"DROP SCHEMA public CASCADE; CREATE SCHEMA public;\"";
        exec($dropCmd, $dropOutput, $dropRes);

        if ($dropRes !== 0) {
            \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
            if (file_exists($autoBackupPath)) unlink($autoBackupPath);
            return back()->with('error', 'Gagal membersihkan database sebelum restore. Restore dibatalkan.');
        }

        // 3. Jalankan restore dari file SQL yang diupload
        $restoreCmd = "\"$psqlPath\" -U $dbUser -h $dbHost -p $dbPort -d $dbName -f \"$sqlPath\"";
        exec($restoreCmd, $restoreOutput, $restoreRes);

        if ($restoreRes !== 0) {
            // Restore gagal! Kembalikan dari backup otomatis
            $rollbackDropCmd = "\"$psqlPath\" -U $dbUser -h $dbHost -p $dbPort -d $dbName -c \"DROP SCHEMA public CASCADE; CREATE SCHEMA public;\"";
            exec($rollbackDropCmd);
            
            $rollbackRestoreCmd = "\"$psqlPath\" -U $dbUser -h $dbHost -p $dbPort -d $dbName -f \"$autoBackupPath\"";
            exec($rollbackRestoreCmd);

            \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
            if (file_exists($autoBackupPath)) unlink($autoBackupPath);
            return back()->with('error', 'Gagal memulihkan database dari file backup. Database telah dikembalikan ke kondisi semula.');
        }

        // Sukses! Bersihkan file temp dan backup otomatis
        \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
        if (file_exists($autoBackupPath)) unlink($autoBackupPath);

        return back()->with('success', 'Database berhasil dipulihkan dari file backup.');
    }
}
