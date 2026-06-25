<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $payment->invoice_number }}</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            margin: 0;
            padding: 10px 20px;
        }

        /* Colors */
        .text-primary { color: #1e3a8a; } /* Deep blue for premium institutional look */
        .bg-primary { background-color: #1e3a8a; color: #ffffff; }
        .text-muted { color: #64748b; }
        
        /* Typography */
        h1, h2, h3, p { margin: 0; padding: 0; }
        .font-bold { font-weight: bold; }
        .text-sm { font-size: 9px; }
        .text-lg { font-size: 14px; }
        .text-xl { font-size: 18px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Layout */
        .w-full { width: 100%; }
        
        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-table td { vertical-align: middle; }
        .logo-cell { width: 70px; text-align: left; }
        .logo-cell img { width: 60px; height: auto; }
        .company-info h1 {
            color: #1e3a8a;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        .company-info p {
            color: #475569;
            font-size: 10px;
            line-height: 1.3;
        }
        
        /* Document Title */
        .doc-title-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .doc-title {
            font-size: 20px;
            color: #1e3a8a;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .invoice-meta {
            text-align: right;
            font-size: 10px;
            color: #475569;
        }

        /* Info Section */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
        }
        .info-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 8px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            border: 1px solid #1e3a8a;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            color: #334155;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .items-table th.text-center, .items-table td.text-center { text-align: center; }
        .items-table th.text-right, .items-table td.text-right { text-align: right; }
        
        /* Totals */
        .total-row td {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e3a8a;
            border-top: 2px solid #1e3a8a;
            font-size: 12px;
        }

        /* Terbilang */
        .terbilang-box {
            background-color: #f0fdf4;
            border-left: 3px solid #16a34a;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 10px;
            color: #166534;
        }

        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 10px;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-title {
            color: #64748b;
            font-size: 10px;
            margin-bottom: 40px;
        }
        .signature-name {
            font-weight: bold;
            color: #1e293b;
            border-bottom: 1px solid #cbd5e1;
            display: inline-block;
            padding-bottom: 2px;
            min-width: 120px;
        }
        .signature-role {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
        }

        /* Footer */
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }

        @php
            $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
            
            function terbilang($angka) {
                $angka = abs($angka);
                $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
                $terbilang = "";
                if ($angka < 12) {
                    $terbilang = " " . $baca[$angka];
                } else if ($angka < 20) {
                    $terbilang = terbilang($angka - 10) . " belas";
                } else if ($angka < 100) {
                    $terbilang = terbilang($angka / 10) . " puluh" . terbilang($angka % 10);
                } else if ($angka < 200) {
                    $terbilang = " seratus" . terbilang($angka - 100);
                } else if ($angka < 1000) {
                    $terbilang = terbilang($angka / 100) . " ratus" . terbilang($angka % 100);
                } else if ($angka < 2000) {
                    $terbilang = " seribu" . terbilang($angka - 1000);
                } else if ($angka < 1000000) {
                    $terbilang = terbilang($angka / 1000) . " ribu" . terbilang($angka % 1000);
                } else if ($angka < 1000000000) {
                    $terbilang = terbilang($angka / 1000000) . " juta" . terbilang($angka % 1000000);
                }
                return $terbilang;
            }
        @endphp
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('favicon.png') }}" alt="Logo">
            </td>
            <td class="company-info">
                <h1>YAYASAN PENDIDIKAN SIMAKU</h1>
                <p>Jl. Pendidikan No. 123, Kota Cendekia, Telp. (021) 1234567<br>
                Email: info@simaku.sch.id | Website: www.simaku.sch.id</p>
            </td>
        </tr>
    </table>

    <!-- Title and Meta -->
    <table class="doc-title-table">
        <tr>
            <td>
                <div class="doc-title">BUKTI PEMBAYARAN</div>
            </td>
            <td class="invoice-meta">
                <span class="font-bold">No. Referensi:</span> {{ $payment->invoice_number }}<br>
                <span class="font-bold">Tanggal:</span> {{ date('d F Y', strtotime($payment->date)) }}
            </td>
        </tr>
    </table>

    <!-- Info Section (Billed To) -->
    <table class="info-table">
        <tr>
            <td style="width: 48%;">
                <div class="info-box">
                    <div class="info-label">Diterima Dari (Data Siswa)</div>
                    <div class="info-value" style="font-size: 13px; margin-bottom: 4px; color: #1e3a8a;">
                        {{ $payment->student->name }}
                    </div>
                    <div class="info-value" style="font-weight: normal; color: #475569;">
                        NIS/NISN: {{ $payment->student->nis ?? '-' }}<br>
                        Kelas: {{ $payment->student->classroom ? $payment->student->classroom->level . ' ' . $payment->student->classroom->name : 'Belum Ada Kelas' }}
                    </div>
                </div>
            </td>
            <td style="width: 4%;"></td> <!-- Spacer -->
            <td style="width: 48%;">
                <div class="info-box" style="height: 100%;">
                    <div class="info-label">Informasi Transaksi</div>
                    <div class="info-value" style="font-weight: normal; color: #475569; padding-top: 4px;">
                        <table style="width: 100%; font-size: 11px;">
                            <tr>
                                <td style="width: 40%; padding-bottom: 3px;">Status</td>
                                <td style="width: 60%; font-weight: bold; color: #16a34a;">: LUNAS</td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 3px;">Metode</td>
                                <td>: Tunai / Transfer</td>
                            </tr>
                            <tr>
                                <td>Penerima</td>
                                <td>: {{ $payment->user ? $payment->user->name : 'Administrator' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">NO</th>
                <th width="35%">RINCIAN PEMBAYARAN</th>
                <th width="35%">KETERANGAN / PERIODE</th>
                <th width="25%" class="text-right">JUMLAH (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($payment->paymentDetails as $detail)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="font-bold">{{ $detail->billing->category->name }}</td>
                <td>
                    T.A {{ $detail->billing->academicYear->name }}
                    {{ $detail->billing->month ? ' (Bulan ' . $months[$detail->billing->month] . ')' : '' }}
                </td>
                <td class="text-right font-bold">
                    {{ number_format($detail->amount, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right" style="padding-right: 15px;">TOTAL PEMBAYARAN</td>
                <td class="text-right" style="font-size: 14px;">
                    Rp {{ number_format($payment->total_amount, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Terbilang -->
    <div class="terbilang-box">
        <strong>Terbilang:</strong> <em>{{ ucwords(terbilang($payment->total_amount)) }} Rupiah</em>
    </div>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-title">Siswa / Wali Siswa,</div>
                <div class="signature-name">{{ $payment->student->name }}</div>
                <div class="signature-role">Penyetor</div>
            </td>
            <td>
                <!-- Center column can be empty or used for stamp -->
                <div style="color: #cbd5e1; font-size: 10px; margin-top: 20px; border: 1px dashed #cbd5e1; display: inline-block; padding: 10px 20px; border-radius: 5px; opacity: 0.5;">
                    CAP INSTANSI / LUNAS
                </div>
            </td>
            <td>
                <div class="signature-title">Kota Cendekia, {{ date('d F Y', strtotime($payment->date)) }}<br>Penerima / Kasir,</div>
                <div class="signature-name">{{ $payment->user ? $payment->user->name : 'Administrator' }}</div>
                <div class="signature-role">Bag. Keuangan SIMAKU</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        Dokumen ini diterbitkan secara sah oleh Sistem Manajemen Keuangan (SIMAKU) YP. SIMAKU.<br>
        Dicetak otomatis pada {{ date('d/m/Y H:i:s') }}. Transparansi dan Akuntabilitas adalah Komitmen Kami.
    </div>

</body>
</html>
