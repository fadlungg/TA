<?php

namespace App\Http\Controllers;

use App\Models\Pemilik;
use App\Models\RiwayatKepemilikan;
use App\Models\Tanah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MutasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $data = RiwayatKepemilikan::with(['tanah', 'pemilikLama', 'pemilikBaru'])
            ->orderBy('tanggal_mutasi', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mutasi.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $tanahList = Tanah::with('pemilik')->orderBy('no_sertifikat')->get();
        $pemilikList = Pemilik::orderBy('nama')->get();

        return view('mutasi.create', compact('tanahList', 'pemilikList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanah_id' => 'required|exists:tanah,id',
            'pemilik_baru_id' => 'required|exists:pemilik,id',
            'jenis_mutasi' => 'required|in:jual_beli,waris,hibah,tukar_guling',
            'tanggal_mutasi' => 'required|date|before_or_equal:today',
            'keterangan' => 'nullable|string',
            'dokumen_mutasi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $tanah = Tanah::findOrFail($validated['tanah_id']);

        // Check if new owner is already the active owner
        if ($tanah->pemilik_id == $validated['pemilik_baru_id']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['pemilik_baru_id' => 'Pemilik baru tidak boleh sama dengan pemilik aktif saat ini.']);
        }

        $dokumenPath = null;
        if ($request->hasFile('dokumen_mutasi')) {
            $file = $request->file('dokumen_mutasi');
            $dokumenPath = $file->store('mutasi', 'public');
        }

        DB::transaction(function () use ($tanah, $validated, $dokumenPath) {
            // Log historic mutation entry
            RiwayatKepemilikan::create([
                'tanah_id' => $tanah->id,
                'pemilik_lama_id' => $tanah->pemilik_id,
                'pemilik_baru_id' => $validated['pemilik_baru_id'],
                'jenis_mutasi' => $validated['jenis_mutasi'],
                'tanggal_mutasi' => $validated['tanggal_mutasi'],
                'dokumen_path' => $dokumenPath,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Update land owner id to new owner
            $tanah->update([
                'pemilik_id' => $validated['pemilik_baru_id'],
            ]);
        });

        return redirect()->route('mutasi.index')
            ->with('success', 'Transaksi mutasi kepemilikan berhasil dicatat.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $mutasi = RiwayatKepemilikan::findOrFail($id);

        // Security check: cannot delete initial registration log from here
        if ($mutasi->jenis_mutasi === 'pendaftaran' || is_null($mutasi->pemilik_lama_id)) {
            return redirect()->back()
                ->withErrors(['error' => 'Riwayat pendaftaran awal tidak dapat dihapus melalui menu ini. Silakan hapus data tanah terkait jika ingin membatalkannya.']);
        }

        DB::transaction(function () use ($mutasi) {
            $tanah = Tanah::findOrFail($mutasi->tanah_id);

            // Revert ownership if the deleted mutation represents the current owner
            if ($tanah->pemilik_id == $mutasi->pemilik_baru_id) {
                $tanah->update([
                    'pemilik_id' => $mutasi->pemilik_lama_id,
                ]);
            }

            // Delete associated file
            if ($mutasi->dokumen_path) {
                Storage::disk('public')->delete($mutasi->dokumen_path);
            }

            $mutasi->delete();
        });

        return redirect()->route('mutasi.index')
            ->with('success', 'Catatan mutasi berhasil dihapus dan kepemilikan dibatalkan.');
    }
}
