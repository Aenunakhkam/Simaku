<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $payment->invoice_number }}</title>
    <style>
        /* Base styles */
        @page {
            margin: 15px; /* VERY IMPORTANT: Reduces DomPDF default page margin */
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 8px; 
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* Typography */
        h1, h2, h3, p { margin: 0; padding: 0; }
        .font-bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Layout */
        .w-full { width: 100%; }
        
        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 2px;
            text-align: center;
        }
        .company-info h1 {
            color: #000;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 1px;
        }
        .company-info p {
            color: #000;
            font-size: 7px;
        }
        
        /* Document Title */
        .doc-title-table {
            width: 100%;
            margin-bottom: 2px;
        }
        .doc-title {
            font-size: 11px;
            color: #000;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .invoice-meta {
            text-align: right;
            font-size: 7px;
            color: #000;
        }

        /* Info Section */
        .info-table {
            width: 100%;
            margin-bottom: 3px;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
        }
        .info-box {
            border: 1px solid #000;
            padding: 2px 4px;
        }
        .info-label {
            font-size: 6px;
            color: #000;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
            margin-bottom: 1px;
        }
        .info-value {
            font-size: 7px;
            color: #000;
        }
        .info-value strong {
            font-size: 8px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }
        .items-table th {
            background-color: #f2f2f2;
            color: #000;
            padding: 2px;
            font-size: 7px;
            text-transform: uppercase;
            text-align: left;
            border: 1px solid #000;
        }
        .items-table td {
            padding: 2px;
            border: 1px solid #000;
            color: #000;
            font-size: 7px;
        }
        .items-table th.text-center, .items-table td.text-center { text-align: center; }
        .items-table th.text-right, .items-table td.text-right { text-align: right; }
        
        /* Totals */
        .total-row td {
            background-color: #e6e6e6;
            font-weight: bold;
            color: #000;
            font-size: 8px;
        }

        /* Terbilang */
        .terbilang-box {
            border: 1px solid #000;
            padding: 2px 4px;
            margin-bottom: 2px;
            font-size: 7px;
            color: #000;
            background-color: #f9f9f9;
        }

        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 2px;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-title {
            color: #000;
            font-size: 7px;
            margin-bottom: 20px; /* space for signature */
        }
        .signature-name {
            font-weight: bold;
            color: #000;
            border-bottom: 1px solid #000;
            display: inline-block;
            padding-bottom: 1px;
            font-size: 8px;
        }
        .signature-role {
            font-size: 6px;
            color: #000;
            margin-top: 1px;
        }

        /* Footer */
        .footer {
            margin-top: 2px;
            text-align: center;
            font-size: 6px;
            color: #000;
            border-top: 1px dashed #000;
            padding-top: 1px;
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
            <td class="company-info">
                <h1>SMK HASYIM ASYARI BOJONG</h1>
                <p>JL.Babakan Tuwel Bojong Tegal 52465</p>
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
                <span class="font-bold">No. Ref:</span> {{ $payment->invoice_number }}<br>
                <span class="font-bold">Tanggal:</span> {{ date('d M Y', strtotime($payment->date)) }}
            </td>
        </tr>
    </table>

    <!-- Info Section (Billed To) -->
    <table class="info-table">
        <tr>
            <td style="width: 49%;">
                <div class="info-box">
                    <div class="info-label">Diterima Dari</div>
                    <div class="info-value">
                        <strong>{{ $payment->student->name }}</strong><br>
                        NIS: {{ $payment->student->nis ?? '-' }} | Kelas: {{ $payment->student->classroom ? $payment->student->classroom->level . ' ' . $payment->student->classroom->name : 'Belum Ada Kelas' }}
                    </div>
                </div>
            </td>
            <td style="width: 2%;"></td>
            <td style="width: 49%;">
                <div class="info-box">
                    <div class="info-label">Informasi Transaksi</div>
                    <div class="info-value">
                        <table style="width: 100%; font-size: 7px;">
                            <tr>
                                <td style="width: 30%;">Status</td>
                                <td style="width: 70%;">: <strong>LUNAS</strong></td>
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
                <th width="40%">RINCIAN PEMBAYARAN</th>
                <th width="30%">PERIODE</th>
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
                <td colspan="3" class="text-right" style="padding-right: 10px;">TOTAL PEMBAYARAN</td>
                <td class="text-right" style="font-size: 8px;">
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
                <div style="font-size: 8px; border: 1px dashed #000; display: inline-block; padding: 2px 6px; border-radius: 3px; color: #000;">
                    LUNAS
                </div>
            </td>
            <td>
                <div class="signature-title">Bojong, {{ date('d M Y', strtotime($payment->date)) }}<br>Penerima,</div>
                <div class="signature-name">{{ $payment->user ? $payment->user->name : 'Admin' }}</div>
                <div class="signature-role">Keuangan</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        Dokumen ini diterbitkan oleh SIMAKU. Dicetak otomatis pada {{ date('d/m/Y H:i') }}.
    </div>

</body>
</html>
