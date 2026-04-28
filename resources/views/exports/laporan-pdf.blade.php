<!DOCTYPE html>
<html>
<head>
    <title>Laporan Posyandu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #999;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        .summary-box strong {
            font-size: 18px;
            display: block;
            margin-bottom: 5px;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
            padding-right: 50px;
        }
        .signature-line {
            display: inline-block;
            width: 200px;
            border-top: 1px solid #333;
            margin-top: 60px;
            text-align: center;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN BULANAN POSYANDU</h1>
        <p>{{ $posyandu->nama ?? 'Posyandu Terpadu' }}</p>
        <p>Alamat: {{ $posyandu->alamat ?? 'Desa / Kelurahan Setempat' }}</p>
        <p><strong>Periode: Bulan {{ $month }} Tahun {{ $year }}</strong></p>
    </div>

    <div style="width: 100%;">
        <div style="float: left; width: 48%;">
            <div class="summary-box">
                <strong>{{ $total_anak }}</strong>
                Total Balita Terdaftar
            </div>
        </div>
        <div style="float: right; width: 48%;">
            <div class="summary-box">
                <strong>{{ $total_hadir }}</strong>
                Balita Hadir Bulan Ini
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <h3>1. Rekapitulasi Status Gizi (BB/U)</h3>
    <table>
        <thead>
            <tr>
                <th>Status Gizi</th>
                <th>Jumlah (Anak)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gizi as $status => $count)
            <tr>
                <td>{{ $status }}</td>
                <td>{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>2. Rekapitulasi Status Pertumbuhan / Stunting (TB/U)</h3>
    <table>
        <thead>
            <tr>
                <th>Status Pertumbuhan</th>
                <th>Jumlah (Anak)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stunting as $status => $count)
            <tr>
                <td>{{ $status }}</td>
                <td>{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p>Mengetahui,</p>
        <p><strong>Ketua Kader Posyandu</strong></p>
        
        <br><br><br>
        
        <span class="signature-line">(...................................................)</span>
    </div>

</body>
</html>
