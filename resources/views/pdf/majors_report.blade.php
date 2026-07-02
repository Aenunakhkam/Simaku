<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 14px;
            margin: 0;
            font-weight: normal;
        }
        .header p {
            font-size: 10px;
            margin: 5px 0 0 0;
        }
        .report-info {
            margin-bottom: 15px;
        }
        .report-info p {
            margin: 3px 0;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-space {
            height: 70px;
        }
        .page-number:before {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <h2>Tahun Ajaran: {{ $academicYear ? $academicYear->name : 'Semua' }}</h2>
    </div>

    <div class="report-info">
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
        <p>Dicetak Oleh: {{ auth()->check() ? auth()->user()->name : 'Administrator' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Siswa</th>
                <th style="width: 15%;">NISN / NIS</th>
                <th style="width: 15%;">Kelas / Jurusan</th>
                <th style="width: 15%;">Status Pembayaran</th>
                <th style="width: 13%;">Total Tagihan</th>
                <th style="width: 12%;">Sisa Tunggakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $s)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $s->nama }}</td>
                    <td class="text-center">{{ $s->nisn }}</td>
                    <td class="text-center">{{ $s->kelas_jurusan }}</td>
                    <td class="text-center">
                        @if($s->status === 'Lunas')
                            <span style="color: green; font-weight: bold;">LUNAS</span>
                        @elseif($s->status === 'Belum Lunas')
                            <span style="color: #d97706; font-weight: bold;">BELUM LUNAS</span>
                        @else
                            <span style="color: red; font-weight: bold;">BELUM BAYAR</span>
                        @endif
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($s->total_tagihan_rp, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($s->sisa_tunggakan_rp, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada siswa yang sesuai dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div style="float: left; width: 50%; color: #666; font-size: 10px; margin-top: 20px;">
            <p><strong>SIMAKU</strong> - Sistem Informasi Manajemen Keuangan Umum</p>
            <p><i>Dokumen ini di-generate secara otomatis oleh sistem pada {{ date('d F Y, H:i') }}</i></p>
            <p>Halaman <span class="page-number"></span></p>
        </div>
        <div class="signature-box">
            <p>................., {{ date('d F Y') }}</p>
            <p>Kepala Instansi / Bendahara</p>
            <div class="signature-space"></div>
            <p><strong>_________________________</strong></p>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
