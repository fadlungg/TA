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
        .text-right {
            text-align: right;
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
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sipektatu - Sistem Kepemilikan Tanah</h1>
        <p>Laporan Data Registrasi Bidang Tanah</p>
    </div>

    <div class="meta-info">
        <div>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }}</div>
        <div>Jumlah Bidang: <strong>{{ $data->count() }} Bidang</strong> | Total Luas: <strong>{{ number_format($data->sum('luas'), 0, ',', '.') }} m²</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>No. Sertifikat</th>
                <th class="text-right">Luas (m²)</th>
                <th>Alamat</th>
                <th>Kecamatan</th>
                <th>Desa</th>
                <th>Jenis Hak</th>
                <th>Status</th>
                <th>Pemilik Aktif</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $idx => $t)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $t->no_sertifikat }}</td>
                    <td class="text-right">{{ number_format($t->luas, 0, ',', '.') }}</td>
                    <td>{{ $t->alamat }}</td>
                    <td>{{ $t->wilayah->nama_kecamatan }}</td>
                    <td>{{ $t->wilayah->nama_desa }}</td>
                    <td>{{ $t->jenisHak->kode }}</td>
                    <td>{{ $t->statusTanah->nama }}</td>
                    <td>{{ $t->pemilik->nama }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada data bidang tanah terdaftar.</td>
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
