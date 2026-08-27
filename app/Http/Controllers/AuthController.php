<?php

namespace App\Http\Controllers;

use App\Models\JenisHak;
use App\Models\Pemilik;
use App\Models\RiwayatKepemilikan;
use App\Models\Tanah;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function login(): View|RedirectResponse
    {
        if (session()->has('admin_logged_in')) {
            return redirect()->route('dashboard');
        }

        return view('login');
    }

    /**
     * Handle the unauthenticated login action.
     */
    public function loginAction(Request $request): RedirectResponse
    {
        $username = $request->input('username');
        if (empty($username)) {
            $username = 'sipektatu';
        }

        session([
            'admin_logged_in' => true,
            'admin_username' => $username,
        ]);

        return redirect()->route('dashboard')->with('success', 'Selamat datang kembali, '.$username.'!');
    }

    /**
     * Show the dashboard with stats and activities.
     */
    public function dashboard(): View|RedirectResponse
    {
        if (! session()->has('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 1. Statistics Cards
        $totalTanah = Tanah::count();
        $totalPemilik = Pemilik::count();
        $totalLuas = Tanah::sum('luas');

        // 2. Split count per Jenis Hak
        $jenisHakDistribution = JenisHak::leftJoin('tanah', 'jenis_hak.id', '=', 'tanah.jenis_hak_id')
            ->select('jenis_hak.kode', 'jenis_hak.nama')
            ->selectRaw('count(tanah.id) as jumlah_tanah')
            ->groupBy('jenis_hak.id', 'jenis_hak.kode', 'jenis_hak.nama')
            ->orderBy('jumlah_tanah', 'desc')
            ->get();

        // 3. Distribution per Wilayah
        $wilayahDistribution = Wilayah::leftJoin('tanah', 'wilayah.id', '=', 'tanah.wilayah_id')
            ->select('wilayah.nama_desa', 'wilayah.nama_kecamatan')
            ->selectRaw('count(tanah.id) as jumlah_tanah')
            ->selectRaw('coalesce(sum(tanah.luas), 0) as total_luas')
            ->groupBy('wilayah.id', 'wilayah.nama_desa', 'wilayah.nama_kecamatan')
            ->orderBy('jumlah_tanah', 'desc')
            ->take(10)
            ->get();

        // 4. Recent Activities (latest 7 inputs/updates from RiwayatKepemilikan)
        $recentActivities = RiwayatKepemilikan::with(['tanah', 'pemilikLama', 'pemilikBaru'])
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();

        return view('dashboard', compact(
            'totalTanah',
            'totalPemilik',
            'totalLuas',
            'jenisHakDistribution',
            'wilayahDistribution',
            'recentActivities'
        ));
    }

    /**
     * Handle the logout action.
     */
    public function logout(): RedirectResponse
    {
        session()->forget(['admin_logged_in', 'admin_username']);
        session()->regenerate();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
