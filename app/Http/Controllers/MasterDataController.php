<?php

namespace App\Http\Controllers;

use App\Models\JenisHak;
use App\Models\StatusTanah;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    // ==========================================
    // JENIS HAK CRUD
    // ==========================================

    /**
     * Show list of Jenis Hak.
     */
    public function jenisHakIndex(): View
    {
        $data = JenisHak::orderBy('kode')->get();

        return view('master-data.jenis-hak', compact('data'));
    }

    /**
     * Store new Jenis Hak.
     */
    public function jenisHakStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
        ]);

        JenisHak::create($validated);

        return redirect()->route('master-data.jenis-hak.index')
            ->with('success', 'Jenis Hak Tanah berhasil ditambahkan.');
    }

    /**
     * Update existing Jenis Hak.
     */
    public function jenisHakUpdate(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
        ]);

        $item = JenisHak::findOrFail($id);
        $item->update($validated);

        return redirect()->route('master-data.jenis-hak.index')
            ->with('success', 'Jenis Hak Tanah berhasil diperbarui.');
    }

    /**
     * Delete Jenis Hak.
     */
    public function jenisHakDestroy(int $id): RedirectResponse
    {
        $item = JenisHak::findOrFail($id);

        if ($item->tanah()->exists()) {
            return redirect()->route('master-data.jenis-hak.index')
                ->with('error', 'Tidak dapat menghapus Jenis Hak ini karena masih terhubung dengan data Tanah.');
        }

        $item->delete();

        return redirect()->route('master-data.jenis-hak.index')
            ->with('success', 'Jenis Hak Tanah berhasil dihapus.');
    }

    // ==========================================
    // WILAYAH CRUD
    // ==========================================

    /**
     * Show list of Wilayah.
     */
    public function wilayahIndex(): View
    {
        $data = Wilayah::orderBy('nama_kecamatan')->orderBy('nama_desa')->get();

        return view('master-data.wilayah', compact('data'));
    }

    /**
     * Store new Wilayah.
     */
    public function wilayahStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kecamatan' => 'nullable|string|max:255',
            'nama_desa' => 'nullable|string|max:255',
            'nama_dusun' => 'nullable|string|max:255',
            'no_rw' => 'nullable|string|max:50',
            'no_rt' => 'nullable|string|max:50',
        ]);

        $validated['nama_kecamatan'] = $validated['nama_kecamatan'] ?: 'Kutoarjo';
        $validated['nama_desa'] = $validated['nama_desa'] ?: 'Tunggorono';

        Wilayah::create($validated);

        return redirect()->route('master-data.wilayah.index')
            ->with('success', 'Wilayah RT/RW/Dusun berhasil ditambahkan.');
    }

    /**
     * Update existing Wilayah.
     */
    public function wilayahUpdate(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kecamatan' => 'nullable|string|max:255',
            'nama_desa' => 'nullable|string|max:255',
            'nama_dusun' => 'nullable|string|max:255',
            'no_rw' => 'nullable|string|max:50',
            'no_rt' => 'nullable|string|max:50',
        ]);

        $validated['nama_kecamatan'] = $validated['nama_kecamatan'] ?: 'Kutoarjo';
        $validated['nama_desa'] = $validated['nama_desa'] ?: 'Tunggorono';

        $item = Wilayah::findOrFail($id);
        $item->update($validated);

        return redirect()->route('master-data.wilayah.index')
            ->with('success', 'Wilayah RT/RW/Dusun berhasil diperbarui.');
    }

    /**
     * Delete Wilayah.
     */
    public function wilayahDestroy(int $id): RedirectResponse
    {
        $item = Wilayah::findOrFail($id);

        if ($item->tanah()->exists()) {
            return redirect()->route('master-data.wilayah.index')
                ->with('error', 'Tidak dapat menghapus Wilayah ini karena masih terhubung dengan data Tanah.');
        }

        $item->delete();

        return redirect()->route('master-data.wilayah.index')
            ->with('success', 'Wilayah berhasil dihapus.');
    }

    // ==========================================
    // STATUS TANAH CRUD
    // ==========================================

    /**
     * Show list of Status Tanah.
     */
    public function statusTanahIndex(): View
    {
        $data = StatusTanah::orderBy('nama')->get();

        return view('master-data.status-tanah', compact('data'));
    }

    /**
     * Store new Status Tanah.
     */
    public function statusTanahStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        StatusTanah::create($validated);

        return redirect()->route('master-data.status-tanah.index')
            ->with('success', 'Status Tanah berhasil ditambahkan.');
    }

    /**
     * Update existing Status Tanah.
     */
    public function statusTanahUpdate(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $item = StatusTanah::findOrFail($id);
        $item->update($validated);

        return redirect()->route('master-data.status-tanah.index')
            ->with('success', 'Status Tanah berhasil diperbarui.');
    }

    /**
     * Delete Status Tanah.
     */
    public function statusTanahDestroy(int $id): RedirectResponse
    {
        $item = StatusTanah::findOrFail($id);

        if ($item->tanah()->exists()) {
            return redirect()->route('master-data.status-tanah.index')
                ->with('error', 'Tidak dapat menghapus Status Tanah ini karena masih terhubung dengan data Tanah.');
        }

        $item->delete();

        return redirect()->route('master-data.status-tanah.index')
            ->with('success', 'Status Tanah berhasil dihapus.');
    }
}
