<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function index()
    {
        $updates = [
            [
                'version' => 'v1.1.0',
                'date' => '16 Juni 2026',
                'author' => 'Aenunakhkam',
                'github' => 'https://github.com/Aenunakhkam/Simaku',
                'changes' => [
                    'Perbaikan bug format Rupiah pada nominal default di kategori pembayaran.',
                    'Pembaruan desain antarmuka (UI) menggunakan konsep modern Sidebar responsif.',
                    'Penambahan Dashboard analisis keuangan tingkat lanjut (Total Kas, Pemasukan, Pengeluaran, Tunggakan).',
                    'Implementasi notifikasi berbasis SweetAlert2.',
                ]
            ],
            [
                'version' => 'v1.0.0',
                'date' => 'Awal Rilis',
                'author' => 'Aenunakhkam',
                'github' => 'https://github.com/Aenunakhkam/Simaku',
                'changes' => [
                    'Rilis awal aplikasi SIMAKU.',
                    'Fitur manajemen master data: Kelas, Jurusan, Siswa.',
                    'Fitur manajemen kategori pemasukan dan pengeluaran.',
                    'Sistem login dan otentikasi admin sekolah.'
                ]
            ]
        ];

        return Inertia::render('Updates', [
            'updates' => $updates
        ]);
    }
}
