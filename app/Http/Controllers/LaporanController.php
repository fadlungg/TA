<?php

namespace App\Http\Controllers;

use App\Models\JenisHak;
use App\Models\RiwayatKepemilikan;
use App\Models\StatusTanah;
use App\Models\Tanah;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Display the reporting dashboard with filter forms.
     */
    public function index(Request $request): View
    {
        $wilayahList = Wilayah::orderBy('nama_kecamatan')->orderBy('nama_desa')->get();
        $jenisHakList = JenisHak::orderBy('kode')->get();
        $statusTanahList = StatusTanah::orderBy('nama')->get();

        // 1. Filtered Lands List
        $tanahQuery = Tanah::with(['jenisHak', 'wilayah', 'statusTanah', 'pemilik']);
        if ($request->filled('wilayah_id')) {
            $tanahQuery->where('wilayah_id', $request->wilayah_id);
        }
        if ($request->filled('jenis_hak_id')) {
            $tanahQuery->where('jenis_hak_id', $request->jenis_hak_id);
        }
        if ($request->filled('status_tanah_id')) {
            $tanahQuery->where('status_tanah_id', $request->status_tanah_id);
        }
        $tanahData = $tanahQuery->get();

        // 2. Area Summary per Village/Wilayah
        $rekapData = Wilayah::leftJoin('tanah', 'wilayah.id', '=', 'tanah.wilayah_id')
            ->select('wilayah.id', 'wilayah.nama_kecamatan', 'wilayah.nama_desa')
            ->selectRaw('count(tanah.id) as jumlah_bidang')
            ->selectRaw('coalesce(sum(tanah.luas), 0) as total_luas')
            ->groupBy('wilayah.id', 'wilayah.nama_kecamatan', 'wilayah.nama_desa')
            ->orderBy('total_luas', 'desc')
            ->get();

        $totalLuasOverall = Tanah::sum('luas');

        // 3. Filtered Mutation Logs
        $start_date = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', now()->toDateString());

        $mutasiQuery = RiwayatKepemilikan::with(['tanah', 'pemilikLama', 'pemilikBaru'])
            ->whereBetween('tanggal_mutasi', [$start_date, $end_date]);

        if ($request->filled('wilayah_id')) {
            $mutasiQuery->whereHas('tanah', function ($q) use ($request) {
                $q->where('wilayah_id', $request->wilayah_id);
            });
        }
        if ($request->filled('jenis_hak_id')) {
            $mutasiQuery->whereHas('tanah', function ($q) use ($request) {
                $q->where('jenis_hak_id', $request->jenis_hak_id);
            });
        }
        $mutasiData = $mutasiQuery->orderBy('tanggal_mutasi', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('laporan.index', compact(
            'wilayahList',
            'jenisHakList',
            'statusTanahList',
            'tanahData',
            'rekapData',
            'totalLuasOverall',
            'mutasiData',
            'start_date',
            'end_date'
        ));
    }

    /**
     * Export filtered lands list to Excel (CSV format).
     */
    public function exportTanah(Request $request): StreamedResponse
    {
        $query = Tanah::with(['jenisHak', 'wilayah', 'statusTanah', 'pemilik']);
        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_id', $request->wilayah_id);
        }
        if ($request->filled('jenis_hak_id')) {
            $query->where('jenis_hak_id', $request->jenis_hak_id);
        }
        if ($request->filled('status_tanah_id')) {
            $query->where('status_tanah_id', $request->status_tanah_id);
        }
        $data = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-bidang-tanah-'.now()->format('YmdHis').'.csv"',
        ];

        return new StreamedResponse(function () use ($data) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($file, ['No', 'No. Sertifikat', 'Luas (m2)', 'Alamat', 'Kecamatan', 'Desa', 'Jenis Hak', 'Status', 'Pemilik Aktif']);

            foreach ($data as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->no_sertifikat,
                    $item->luas,
                    $item->alamat,
                    $item->wilayah->nama_kecamatan,
                    $item->wilayah->nama_desa,
                    $item->jenisHak->nama,
                    $item->statusTanah->nama,
                    $item->pemilik->nama,
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }

    /**
     * Print filtered lands list.
     */
    public function printTanah(Request $request): View
    {
        $query = Tanah::with(['jenisHak', 'wilayah', 'statusTanah', 'pemilik']);
        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_id', $request->wilayah_id);
        }
        if ($request->filled('jenis_hak_id')) {
            $query->where('jenis_hak_id', $request->jenis_hak_id);
        }
        if ($request->filled('status_tanah_id')) {
            $query->where('status_tanah_id', $request->status_tanah_id);
        }
        $data = $query->get();
        $title = 'Laporan Bidang Tanah';

        return view('laporan.print_tanah', compact('data', 'title'));
    }

    /**
     * Export rekap luas per wilayah to Excel (CSV format).
     */
    public function exportRekap(): StreamedResponse
    {
        $data = Wilayah::leftJoin('tanah', 'wilayah.id', '=', 'tanah.wilayah_id')
            ->select('wilayah.id', 'wilayah.nama_kecamatan', 'wilayah.nama_desa')
            ->selectRaw('count(tanah.id) as jumlah_bidang')
            ->selectRaw('coalesce(sum(tanah.luas), 0) as total_luas')
            ->groupBy('wilayah.id', 'wilayah.nama_kecamatan', 'wilayah.nama_desa')
            ->orderBy('total_luas', 'desc')
            ->get();

        $totalLuasOverall = Tanah::sum('luas');

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-rekap-wilayah-'.now()->format('YmdHis').'.csv"',
        ];

        return new StreamedResponse(function () use ($data, $totalLuasOverall) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($file, ['No', 'Kecamatan', 'Desa', 'Jumlah Bidang', 'Total Luas (m2)', 'Persentase Luas (%)']);

            foreach ($data as $index => $item) {
                $pct = $totalLuasOverall > 0 ? ($item->total_luas / $totalLuasOverall) * 100 : 0;
                fputcsv($file, [
                    $index + 1,
                    $item->nama_kecamatan,
                    $item->nama_desa,
                    $item->jumlah_bidang,
                    $item->total_luas,
                    number_format($pct, 2, ',', '.'),
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }

    /**
     * Print rekap luas per wilayah.
     */
    public function printRekap(): View
    {
        $data = Wilayah::leftJoin('tanah', 'wilayah.id', '=', 'tanah.wilayah_id')
            ->select('wilayah.id', 'wilayah.nama_kecamatan', 'wilayah.nama_desa')
            ->selectRaw('count(tanah.id) as jumlah_bidang')
            ->selectRaw('coalesce(sum(tanah.luas), 0) as total_luas')
            ->groupBy('wilayah.id', 'wilayah.nama_kecamatan', 'wilayah.nama_desa')
            ->orderBy('total_luas', 'desc')
            ->get();

        $totalLuasOverall = Tanah::sum('luas');
        $title = 'Laporan Rekapitulasi Luas per Wilayah';

        return view('laporan.print_rekap', compact('data', 'totalLuasOverall', 'title'));
    }

    /**
     * Export filtered mutations list to Excel (CSV format).
     */
    public function exportMutasi(Request $request): StreamedResponse
    {
        $start_date = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', now()->toDateString());

        $query = RiwayatKepemilikan::with(['tanah', 'pemilikLama', 'pemilikBaru'])
            ->whereBetween('tanggal_mutasi', [$start_date, $end_date]);

        if ($request->filled('wilayah_id')) {
            $query->whereHas('tanah', function ($q) use ($request) {
                $q->where('wilayah_id', $request->wilayah_id);
            });
        }
        if ($request->filled('jenis_hak_id')) {
            $query->whereHas('tanah', function ($q) use ($request) {
                $q->where('jenis_hak_id', $request->jenis_hak_id);
            });
        }

        $data = $query->orderBy('tanggal_mutasi', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-mutasi-'.now()->format('YmdHis').'.csv"',
        ];

        return new StreamedResponse(function () use ($data) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($file, ['No', 'No. Sertifikat', 'Pemilik Sebelumnya', 'Pemilik Baru', 'Jenis Mutasi', 'Tanggal Mutasi', 'Keterangan']);

            foreach ($data as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->tanah->no_sertifikat,
                    $item->pemilikLama ? $item->pemilikLama->nama : 'Pendaftaran Awal',
                    $item->pemilikBaru->nama,
                    strtoupper(str_replace('_', ' ', $item->jenis_mutasi)),
                    $item->tanggal_mutasi->format('Y-m-d'),
                    $item->keterangan ?: '-',
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }

    /**
     * Print filtered mutations list.
     */
    public function printMutasi(Request $request): View
    {
        $start_date = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', now()->toDateString());

        $query = RiwayatKepemilikan::with(['tanah', 'pemilikLama', 'pemilikBaru'])
            ->whereBetween('tanggal_mutasi', [$start_date, $end_date]);

        if ($request->filled('wilayah_id')) {
            $query->whereHas('tanah', function ($q) use ($request) {
                $q->where('wilayah_id', $request->wilayah_id);
            });
        }
        if ($request->filled('jenis_hak_id')) {
            $query->whereHas('tanah', function ($q) use ($request) {
                $q->where('jenis_hak_id', $request->jenis_hak_id);
            });
        }

        $data = $query->orderBy('tanggal_mutasi', 'desc')->get();
        $title = 'Laporan Transaksi Mutasi Tanah';

        return view('laporan.print_mutasi', compact('data', 'start_date', 'end_date', 'title'));
    }
}
