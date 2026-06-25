<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi - {{ $payment->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; margin: 15px; color: #333; }
        .kop-surat { width: 100%; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 15px; }
        .kop-surat td { border: none; padding: 0; }
        .kop-surat .logo { width: 60px; text-align: left; }
        .kop-surat .teks { text-align: center; }
        .kop-surat h1 { margin: 0; font-size: 18px; text-transform: uppercase; font-weight: bold; }
        .kop-surat h2 { margin: 3px 0 0 0; font-size: 14px; text-transform: uppercase; font-weight: bold; }
        .kop-surat p { margin: 2px 0 0 0; font-size: 10px; }
        
        .title { text-align: center; margin-bottom: 15px; }
        .title h3 { margin: 0; font-size: 16px; text-transform: uppercase; font-weight: bold; }
        .title p { margin: 2px 0 0 0; font-size: 11px; }

        .info { margin-bottom: 10px; width: 100%; font-size: 11px; }
        .info td { padding: 2px; }
        .info td.label { width: 15%; font-weight: bold; }
        .info td.value { width: 35%; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #000; padding: 6px; text-align: left; }
        table.data th { background-color: #f2f2f2; font-weight: bold; font-size: 10px; text-align: center; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        
        .footer { margin-top: 30px; width: 100%; border: none; }
        .footer td { border: none; text-align: center; vertical-align: top; width: 50%; }
        .signature-box { margin-top: 60px; font-weight: bold; }
        
        @php
            $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
        @endphp
    </style>
</head>
<body>

    <table class="kop-surat">
        <tr>
            <td class="logo">
                <img src="{{ public_path('favicon.png') }}" style="width: 60px;">
            </td>
            <td class="teks">
                <h1>YAYASAN PENDIDIKAN SIMAKU</h1>
                <h2>BUKTI PEMBAYARAN RESMI</h2>
                <p>Jl. Pendidikan No. 123, Telp. (021) 1234567, Email: info@simaku.sch.id</p>
            </td>
        </tr>
    </table>

    <div class="title">
        <h3>KUITANSI PEMBAYARAN</h3>
        <p>No: {{ $payment->invoice_number }}</p>
    </div>

    <table class="info">
        <tr>
            <td class="label">Telah terima dari</td>
            <td colspan="3" class="value">: <strong>{{ $payment->student->name }}</strong></td>
        </tr>
        <tr>
            <td class="label">Siswa Kelas</td>
            <td class="value">: {{ $payment->student->classroom ? $payment->student->classroom->level . ' ' . $payment->student->classroom->name : '-' }}</td>
            <td class="label">Tanggal Bayar</td>
            <td class="value">: {{ date('d F Y', strtotime($payment->date)) }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Untuk Pembayaran</th>
                <th width="35%">Keterangan</th>
                <th width="25%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($payment->paymentDetails as $detail)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $detail->billing->category->name }}</td>
                <td>
                    {{ $detail->billing->academicYear->name }}
                    {{ $detail->billing->month ? ' - Bulan ' . $months[$detail->billing->month] : '' }}
                </td>
                <td class="text-right">Rp {{ number_format($detail->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right" style="font-weight: bold; background-color: #f2f2f2;">TOTAL DIBAYAR</td>
                <td class="text-right" style="font-weight: bold; font-size: 13px;">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @php
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

    <div style="margin-top: 15px; font-style: italic; font-size: 11px;">
        Terbilang: <strong>{{ ucwords(terbilang($payment->total_amount)) }} Rupiah</strong>
    </div>

    <table class="footer">
        <tr>
            <td>
                <br>
                Yang Menyerahkan,
                <div class="signature-box">
                    __________________________<br>
                    (Siswa / Wali Siswa)
                </div>
            </td>
            <td>
                ................., {{ date('d F Y', strtotime($payment->date)) }}<br>
                Kasir / Penerima
                <div class="signature-box">
                    <strong>{{ $payment->user ? $payment->user->name : 'Administrator' }}</strong><br>
                    __________________________
                </div>
            </td>
        </tr>
    </table>
    
    <div style="margin-top: 20px; font-size: 9px; color: #555; font-style: italic; border-top: 1px dashed #ccc; padding-top: 5px;">
        * Dokumen Kuitansi ini dicetak secara otomatis oleh Sistem Manajemen Keuangan (SIMAKU) pada {{ date('d/m/Y H:i:s') }}.
    </div>

</body>
</html>
