<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Massal Slip Tagihan</title>
    <style>
        @page {
            margin: 20px; /* Gunakan margin halaman yang aman */
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 8.5px;
            color: #000;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .receipt-container {
            width: 100%;
            height: 48%; /* Beri ruang 48% agar pas 2 kuitansi di satu halaman */
            box-sizing: border-box;
            position: relative;
            overflow: hidden; /* Mencegah tumpah ke kuitansi di bawahnya */
        }
        .cut-line-wrapper {
            width: 100%;
            height: 4%; /* Ruang 4% untuk garis potong */
            display: table;
        }
        .cut-line {
            border-top: 1px dashed #888;
            margin-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
        
        .kop-surat {
            width: 100%;
            border-bottom: 1.5px solid #000;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }
        .kop-surat td.teks {
            text-align: center;
        }
        .kop-surat h1 { margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .kop-surat p { margin: 0; font-size: 8px; }
        
        .title { text-align: center; margin-bottom: 8px; font-size: 10px; font-weight: bold; text-transform: uppercase; text-decoration: underline; }
        
        .info-table { width: 100%; margin-bottom: 5px; font-size: 8.5px; }
        .info-table td { vertical-align: top; padding: 1px 0; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; table-layout: fixed; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 2.5px 4px; font-size: 8px; word-wrap: break-word; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .summary-box { width: 45%; float: right; border: 1px solid #000; padding: 3px; font-size: 8.5px; }
        .summary-box table { width: 100%; }
        .summary-box td { padding: 1.5px; }
        
        .clear { clear: both; }
        
        .footer-note { margin-top: 5px; font-size: 7.5px; font-style: italic; color: #555; text-align: center; }
    </style>
</head>
<body>
    @php 
        $counter = 0; 
        $totalStudents = count($students);
        $months = [1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun', 7=>'Jul', 8=>'Agu', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des'];
    @endphp
    
    @foreach($students as $index => $student)
        <div class="receipt-container">
            <table class="kop-surat">
                <tr>
                    <td class="teks">
                        <h1>SMK HASYIM ASYARI BOJONG</h1>
                        <p>JL.Babakan Tuwel Bojong Tegal 52465</p>
                    </td>
                </tr>
            </table>

            <div class="title">SLIP TAGIHAN & STATUS PEMBAYARAN</div>

            <table class="info-table">
                <tr>
                    <td width="15%"><strong>Nama Siswa</strong></td>
                    <td width="35%">: {{ $student->name }}</td>
                    <td width="15%"><strong>Tanggal Cetak</strong></td>
                    <td width="35%">: {{ date('d M Y') }}</td>
                </tr>
                <tr>
                    <td><strong>NIS / NISN</strong></td>
                    <td>: {{ $student->nis }} / {{ $student->nisn }}</td>
                    <td><strong>Tahun Ajaran</strong></td>
                    <td>: {{ \App\Models\AcademicYear::find(session('academic_year_id'))->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Kelas</strong></td>
                    <td>: {{ $student->classroom ? $student->classroom->level . ' ' . $student->classroom->name : '-' }}</td>
                    <td><strong>Status Pembayaran</strong></td>
                    <td>: 
                        @if($student->payment_status === 'lunas')
                            <strong style="color: #28a745;">LUNAS</strong>
                        @elseif($student->payment_status === 'nyicil')
                            <strong style="color: #ea580c;">MENCICIL (BELUM LUNAS)</strong>
                        @else
                            <strong style="color: #dc3545;">BELUM BAYAR</strong>
                        @endif
                    </td>
                </tr>
            </table>

            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="35%">Rincian Tagihan</th>
                        <th width="20%">Nominal Tagihan</th>
                        <th width="20%">Terbayar</th>
                        <th width="20%">Sisa</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse($student->billings as $bill)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $bill->category ? $bill->category->name : '-' }} {{ $bill->month ? '(Bln '.$months[$bill->month].')' : '' }}</td>
                        <td class="text-right">{{ number_format($bill->amount, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($bill->paid_amount, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">{{ number_format($bill->remaining_amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada rincian tagihan di tahun ajaran aktif.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="summary-box">
                <table>
                    <tr>
                        <td>Total Tagihan</td>
                        <td class="text-right font-bold">Rp {{ number_format($student->total_bill, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Total Terbayar</td>
                        <td class="text-right font-bold" style="color: #28a745;">Rp {{ number_format($student->total_paid, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px solid #000; padding-top: 2px;">Sisa Tagihan</td>
                        <td class="text-right font-bold" style="color: #dc3545; border-top: 1px solid #000; padding-top: 2px;">Rp {{ number_format($student->remaining, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
            <div class="clear"></div>
            
            <div class="footer-note">
                Dicetak oleh Sistem Manajemen Keuangan (SIMAKU) pada {{ date('d/m/Y H:i:s') }}
            </div>
        </div>
        
        @php $counter++; @endphp
        
        @if($counter % 2 != 0 && $index < $totalStudents - 1)
            <div class="cut-line-wrapper">
                <div class="cut-line"></div>
            </div>
        @endif
        
        @if($counter % 2 == 0 && $index < $totalStudents - 1)
            <div class="page-break"></div>
        @endif
    @endforeach
    
    @if(count($students) == 0)
        <div style="padding: 50px; text-align: center; font-size: 14px;">
            Tidak ada data siswa untuk dicetak.
        </div>
    @endif
</body>
</html>
