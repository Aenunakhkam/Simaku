<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Siswa - {{ $student->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; margin: 20px; color: #000; }
        .kop-surat { width: 100%; border-bottom: 4px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat td { border: none; padding: 0; }
        .kop-surat .logo { width: 80px; text-align: left; }
        .kop-surat .teks { text-align: center; }
        .kop-surat h1 { margin: 0; font-size: 22px; text-transform: uppercase; font-weight: bold; }
        .kop-surat h2 { margin: 5px 0 0 0; font-size: 18px; text-transform: uppercase; font-weight: bold; }
        .kop-surat p { margin: 3px 0 0 0; font-size: 12px; }
        
        .title { text-align: center; margin-bottom: 20px; }
        .title h3 { margin: 0; font-size: 14px; text-transform: uppercase; text-decoration: underline; }
        
        .info { margin-bottom: 15px; width: 100%; font-size: 12px; }
        .info td { padding: 3px; font-weight: bold; border: none; }
        .info td.value { font-weight: normal; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #000; }
        table.data th, table.data td { border: 1px solid #000; padding: 8px 6px; text-align: left; }
        table.data th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 11px; text-align: center; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        
        .footer { margin-top: 50px; width: 100%; border: none; }
        .footer td { border: none; text-align: center; vertical-align: top; width: 50%; }
        .signature-box { margin-top: 80px; font-weight: bold; }
        
        .page-break { page-break-after: always; }
        .page-number:before { content: counter(page); }
    </style>
</head>
<body>

    <table class="kop-surat">
        <tr>
            <td class="logo">
                <img src="{{ public_path('favicon.png') }}" style="width: 80px;">
            </td>
            <td class="teks">
                <h1>YAYASAN PENDIDIKAN SIMAKU</h1>
                <h2>SMK / SMA SIMAKU</h2>
                <p>Jl. Pendidikan No. 123, Telp. (021) 1234567, Email: info@simaku.sch.id</p>
                <p>Website: www.simaku.sch.id</p>
            </td>
        </tr>
    </table>

    <div class="title">
        <h3>RINCIAN TAGIHAN & PEMBAYARAN SISWA</h3>
    </div>

    @php
        $academicYear = \App\Models\AcademicYear::find(session('academic_year_id'));
        $academicYearName = $academicYear ? $academicYear->name : 'Tahun Ajaran Berjalan';
    @endphp

    <table class="info">
        <tr>
            <td width="15%">Nama Siswa</td>
            <td width="35%" class="value">: {{ $student->name }}</td>
            <td width="15%">Kelas</td>
            <td width="35%" class="value">: {{ $student->classroom ? $student->classroom->level . ' ' . $student->classroom->name : '-' }}</td>
        </tr>
        <tr>
            <td>NIS / NISN</td>
            <td class="value">: {{ $student->nis }} / {{ $student->nisn }}</td>
            <td>Tahun Ajaran</td>
            <td class="value">: {{ $academicYearName }}</td>
        </tr>
        <tr>
            <td>Program Keahlian</td>
            <td class="value">: {{ ($student->classroom && $student->classroom->major) ? $student->classroom->major->name : 'Umum' }}</td>
            <td>Tanggal Cetak</td>
            <td class="value">: {{ date('d F Y') }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Kategori Tagihan</th>
                <th width="25%">Nama Tagihan</th>
                <th width="15%">Nominal Tagihan</th>
                <th width="15%">Terbayar</th>
                <th width="10%">Sisa</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 1; $grandTotal = 0; $grandPaid = 0; $grandSisa = 0; 
                $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
            @endphp
            @forelse($billings as $billing)
                @php
                    $grandTotal += $billing->amount;
                    $paidAmount = $billing->paymentDetails->sum('amount');
                    $grandPaid += $paidAmount;
                    $sisa = $billing->amount - $paidAmount;
                    $grandSisa += $sisa;
                    $status = $billing->is_paid ? 'LUNAS' : ($paidAmount > 0 ? 'DICICIL' : 'BELUM');
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $billing->category ? $billing->category->name : '-' }}</td>
                    <td>{{ $billing->month ? 'Bulan ' . $months[$billing->month] : 'Sekali Bayar' }}</td>
                    <td class="text-right">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($paidAmount, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($billing->is_paid)
                            LUNAS
                        @elseif($paidAmount > 0)
                            <span style="color: #ea580c; font-weight: bold;">DICICIL</span>
                        @else
                            BELUM
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Siswa ini tidak memiliki data tagihan di tahun ajaran aktif.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <td colspan="3" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($grandPaid, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($grandSisa, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($grandTotal > 0 && $grandPaid >= $grandTotal)
                        LUNAS
                    @else
                        BELUM
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>

    <table class="footer">
        <tr>
            <td>
                <br>
                Mengetahui,<br>
                Orang Tua / Wali Siswa
                <div class="signature-box">
                    __________________________<br>
                </div>
            </td>
            <td>
                ................., {{ date('d F Y') }}<br>
                Bendahara / Bag. Keuangan
                <div class="signature-box">
                    <strong>{{ auth()->user()->name }}</strong><br>
                    __________________________
                </div>
            </td>
        </tr>
    </table>

    <div style="margin-top: 40px; font-size: 10px; color: #555; font-style: italic; border-top: 1px dashed #ccc; padding-top: 5px;">
        * Dokumen Laporan Rincian Tagihan ini dicetak secara otomatis oleh Sistem Manajemen Keuangan (SIMAKU) pada {{ date('d/m/Y H:i:s') }}.
    </div>

</body>
</html>
