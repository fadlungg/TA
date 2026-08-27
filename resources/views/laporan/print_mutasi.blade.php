<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} - Sipektatu</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #000;
            background: #fff;
            margin: 30px;
            font-size: 13px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #555;
            font-size: 12px;
        }
        .meta-info {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #555;
        }
        @media print {
            body {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sipektatu - Sistem Kepemilikan Tanah</h1>
        <p>Laporan Riwayat Transaksi Mutasi Kepemilikan Tanah</p>
    </div>

    <div class="meta-info">
        <div>Periode: <strong>{{ \Carbon\Carbon::parse($start_date)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y') }}</strong></div>
        <div>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }} | Total Transaksi: <strong>{{ $data->count() }} Mutasi</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>No. Sertifikat</th>
                <th>Pemilik Sebelumnya</th>
                <th>Pemilik Baru (Sekarang)</th>
                <th>Jenis Mutasi</th>
                <th>Tanggal Mutasi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $idx => $m)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $m->tanah->no_sertifikat }}</td>
                    <td>{{ $m->pemilikLama ? $m->pemilikLama->nama : 'Pendaftaran Awal' }}</td>
                    <td>{{ $m->pemilikBaru->nama }}</td>
                    <td>{{ strtoupper(str_replace('_', ' ', $m->jenis_mutasi)) }}</td>
                    <td>{{ $m->tanggal_mutasi->translatedFormat('d M Y') }}</td>
                    <td>{{ $m->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada transaksi mutasi tercatat pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div>Dokumen dicetak otomatis oleh sistem Sipektatu.</div>
        <div>Tanda Tangan Petugas / Administrator</div>
    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>
