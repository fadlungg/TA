<?php

namespace App\Http\Controllers;

use App\Models\Pemilik;
use App\Models\RiwayatKepemilikan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PemilikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $data = Pemilik::withCount('tanah')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pemilik.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $pemilik = new Pemilik;

        return view('pemilik.form', compact('pemilik'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|size:16|unique:pemilik,nik',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $pemilik = new Pemilik($validated);

        if ($request->hasFile('foto_ktp')) {
            $file = $request->file('foto_ktp');
            $path = $file->store('ktp', 'public');
            $pemilik->foto_ktp = $path;
        }

        $pemilik->save();

        return redirect()->route('pemilik.index')
            ->with('success', 'Data Pemilik berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $pemilik = Pemilik::findOrFail($id);

        // Fetch currently owned lands (active)
        $tanahAktif = $pemilik->tanah()
            ->with(['jenisHak', 'wilayah', 'statusTanah'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch mutation history involving this owner
        $riwayatTanah = RiwayatKepemilikan::with(['tanah.jenisHak', 'tanah.wilayah', 'pemilikLama', 'pemilikBaru'])
            ->where(function ($query) use ($id) {
                $query->where('pemilik_lama_id', $id)
                    ->orWhere('pemilik_baru_id', $id);
            })
            ->orderBy('tanggal_mutasi', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pemilik.show', compact('pemilik', 'tanahAktif', 'riwayatTanah'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $pemilik = Pemilik::findOrFail($id);

        return view('pemilik.form', compact('pemilik'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $pemilik = Pemilik::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|size:16|unique:pemilik,nik,'.$id,
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('foto_ktp')) {
            if ($pemilik->foto_ktp) {
                Storage::disk('public')->delete($pemilik->foto_ktp);
            }
            $file = $request->file('foto_ktp');
            $path = $file->store('ktp', 'public');
            $pemilik->foto_ktp = $path;
        }

        $pemilik->fill(collect($validated)->except('foto_ktp')->toArray());
        $pemilik->save();

        return redirect()->route('pemilik.index')
            ->with('success', 'Data Pemilik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $pemilik = Pemilik::findOrFail($id);

        // Security check: cannot delete owner if they still own active lands
        if ($pemilik->tanah()->exists()) {
            return redirect()->back()
                ->withErrors(['error' => 'Data pemilik tidak dapat dihapus karena masih memiliki sertifikat tanah aktif.']);
        }

        // Delete KTP photo from storage if exists
        if ($pemilik->foto_ktp) {
            Storage::disk('public')->delete($pemilik->foto_ktp);
        }

        $pemilik->delete();

        return redirect()->route('pemilik.index')
            ->with('success', 'Data Pemilik berhasil dihapus.');
    }
}
