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
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:50000',
        ]);

        $file = $request->file('backup_file');
        $foundSql = false;

        // Cek validitas zip
        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive();
            $res = $zip->open($file->getRealPath());
            if ($res === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    if (pathinfo($stat['name'], PATHINFO_EXTENSION) === 'sql') {
                        $foundSql = true;
                        break;
                    }
                }
                $zip->close();
            } else {
                return back()->with('error', 'Gagal membaca file ZIP.');
            }
        } else {
            // Fallback: list isi zip menggunakan tar
            $zipRealPath = $file->getRealPath();
            exec("tar -t -f \"$zipRealPath\"", $tarList, $tarRes);
            if ($tarRes === 0) {
                foreach ($tarList as $item) {
                    if (pathinfo($item, PATHINFO_EXTENSION) === 'sql') {
                        $foundSql = true;
                        break;
                    }
                }
            } else {
                return back()->with('error', 'Gagal membaca file ZIP menggunakan tar.');
            }
        }

        if (!$foundSql) {
            return back()->with('error', 'File ZIP tidak valid. Tidak ada file .sql ditemukan.');
        }

        return back()->with('error', 'Fitur eksekusi Restore otomatis masih dinonaktifkan demi keamanan. Silakan ekstrak file ZIP dan import .sql secara manual.');
    }
}
