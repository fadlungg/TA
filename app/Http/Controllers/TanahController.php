<?php

namespace App\Http\Controllers;

use App\Models\JenisHak;
use App\Models\Pemilik;
use App\Models\StatusTanah;
use App\Models\Tanah;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TanahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $data = Tanah::with(['wilayah', 'jenisHak', 'statusTanah', 'pemilik'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tanah.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $jenisHakList = JenisHak::orderBy('kode')->get();
        $wilayahList = Wilayah::orderBy('nama_kecamatan')->orderBy('nama_desa')->get();
        $statusTanahList = StatusTanah::orderBy('nama')->get();
        $pemilikList = Pemilik::orderBy('nama')->get();

        $tanah = new Tanah; // Empty model for sharing form view between create/edit

        return view('tanah.form', compact('jenisHakList', 'wilayahList', 'statusTanahList', 'pemilikList', 'tanah'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'no_sertifikat' => 'required|string|max:255|unique:tanah,no_sertifikat',
            'no_letter_c' => 'nullable|string|max:255',
            'no_persil' => 'nullable|string|max:255',
            'klas_tanah' => 'nullable|string|max:255',
            'status_bengkok' => 'nullable|string|max:255',
            'jenis_hak_id' => 'required|exists:jenis_hak,id',
            'luas' => 'required|numeric|min:0.01',
            'alamat' => 'required|string',
            'wilayah_id' => 'required|exists:wilayah,id',
            'status_tanah_id' => 'required|exists:status_tanah,id',
            'pemilik_id' => 'required|exists:pemilik,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'dokumen_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto_lokasi' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Create Land
            $tanah = Tanah::create([
                'no_sertifikat' => $validated['no_sertifikat'],
                'no_letter_c' => $validated['no_letter_c'] ?? null,
                'no_persil' => $validated['no_persil'] ?? null,
                'klas_tanah' => $validated['klas_tanah'] ?? null,
                'status_bengkok' => $validated['status_bengkok'] ?? null,
                'jenis_hak_id' => $validated['jenis_hak_id'],
                'luas' => $validated['luas'],
                'alamat' => $validated['alamat'],
                'wilayah_id' => $validated['wilayah_id'],
                'status_tanah_id' => $validated['status_tanah_id'],
                'pemilik_id' => $validated['pemilik_id'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
            ]);

            // Handle Document Uploads
            if ($request->hasFile('dokumen_sertifikat')) {
                $file = $request->file('dokumen_sertifikat');
                $path = $file->store('dokumen', 'public');
                $tanah->dokumenTanah()->create([
                    'nama_dokumen' => 'Scan Sertifikat',
                    'file_path' => $path,
                    'uploaded_at' => now(),
                ]);
            }

            if ($request->hasFile('foto_lokasi')) {
                $file = $request->file('foto_lokasi');
                $path = $file->store('dokumen', 'public');
                $tanah->dokumenTanah()->create([
                    'nama_dokumen' => 'Foto Lokasi',
                    'file_path' => $path,
                    'uploaded_at' => now(),
                ]);
            }

            // Create Initial History Log
            $tanah->riwayatKepemilikan()->create([
                'pemilik_lama_id' => null,
                'pemilik_baru_id' => $validated['pemilik_id'],
                'jenis_mutasi' => 'pendaftaran',
                'tanggal_mutasi' => now()->toDateString(),
                'keterangan' => 'Pendaftaran tanah pertama kali ke sistem Sipektatu.',
            ]);
        });

        return redirect()->route('tanah.index')
            ->with('success', 'Data Tanah berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $tanah = Tanah::with([
            'jenisHak', 'wilayah', 'statusTanah', 'pemilik',
            'dokumenTanah',
            'riwayatKepemilikan' => function ($q) {
                $q->with(['pemilikLama', 'pemilikBaru'])->orderBy('tanggal_mutasi', 'desc')->orderBy('created_at', 'desc');
            },
        ])->findOrFail($id);

        return view('tanah.show', compact('tanah'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $tanah = Tanah::findOrFail($id);
        $jenisHakList = JenisHak::orderBy('kode')->get();
        $wilayahList = Wilayah::orderBy('nama_kecamatan')->orderBy('nama_desa')->get();
        $statusTanahList = StatusTanah::orderBy('nama')->get();
        $pemilikList = Pemilik::orderBy('nama')->get();

        return view('tanah.form', compact('tanah', 'jenisHakList', 'wilayahList', 'statusTanahList', 'pemilikList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $tanah = Tanah::findOrFail($id);

        $validated = $request->validate([
            'no_sertifikat' => 'required|string|max:255|unique:tanah,no_sertifikat,'.$id,
            'no_letter_c' => 'nullable|string|max:255',
            'no_persil' => 'nullable|string|max:255',
            'klas_tanah' => 'nullable|string|max:255',
            'status_bengkok' => 'nullable|string|max:255',
            'jenis_hak_id' => 'required|exists:jenis_hak,id',
            'luas' => 'required|numeric|min:0.01',
            'alamat' => 'required|string',
            'wilayah_id' => 'required|exists:wilayah,id',
            'status_tanah_id' => 'required|exists:status_tanah,id',
            'pemilik_id' => 'required|exists:pemilik,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'dokumen_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto_lokasi' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        DB::transaction(function () use ($tanah, $validated, $request) {
            $oldOwnerId = $tanah->pemilik_id;
            $newOwnerId = $validated['pemilik_id'];

            // Update Land plot
            $tanah->update([
                'no_sertifikat' => $validated['no_sertifikat'],
                'no_letter_c' => $validated['no_letter_c'] ?? null,
                'no_persil' => $validated['no_persil'] ?? null,
                'klas_tanah' => $validated['klas_tanah'] ?? null,
                'status_bengkok' => $validated['status_bengkok'] ?? null,
                'jenis_hak_id' => $validated['jenis_hak_id'],
                'luas' => $validated['luas'],
                'alamat' => $validated['alamat'],
                'wilayah_id' => $validated['wilayah_id'],
                'status_tanah_id' => $validated['status_tanah_id'],
                'pemilik_id' => $validated['pemilik_id'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
            ]);

            // Handle Document Uploads
            if ($request->hasFile('dokumen_sertifikat')) {
                // Remove old sertifikat document if exists
                $oldSertifikat = $tanah->dokumenTanah()->where('nama_dokumen', 'Scan Sertifikat')->first();
                if ($oldSertifikat) {
                    Storage::disk('public')->delete($oldSertifikat->file_path);
                    $oldSertifikat->delete();
                }

                $file = $request->file('dokumen_sertifikat');
                $path = $file->store('dokumen', 'public');
                $tanah->dokumenTanah()->create([
                    'nama_dokumen' => 'Scan Sertifikat',
                    'file_path' => $path,
                    'uploaded_at' => now(),
                ]);
            }

            if ($request->hasFile('foto_lokasi')) {
                // Remove old foto if exists
                $oldFoto = $tanah->dokumenTanah()->where('nama_dokumen', 'Foto Lokasi')->first();
                if ($oldFoto) {
                    Storage::disk('public')->delete($oldFoto->file_path);
                    $oldFoto->delete();
                }

                $file = $request->file('foto_lokasi');
                $path = $file->store('dokumen', 'public');
                $tanah->dokumenTanah()->create([
                    'nama_dokumen' => 'Foto Lokasi',
                    'file_path' => $path,
                    'uploaded_at' => now(),
                ]);
            }

            // Create Mutation History Log if Owner Changed
            if ($oldOwnerId != $newOwnerId) {
                $tanah->riwayatKepemilikan()->create([
                    'pemilik_lama_id' => $oldOwnerId,
                    'pemilik_baru_id' => $newOwnerId,
                    'jenis_mutasi' => 'jual_beli', // default mutasi
                    'tanggal_mutasi' => now()->toDateString(),
                    'keterangan' => 'Mutasi kepemilikan melalui pembaruan data tanah.',
                ]);
            }
        });

        return redirect()->route('tanah.index')
            ->with('success', 'Data Tanah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $tanah = Tanah::findOrFail($id);

        // Delete files from storage
        foreach ($tanah->dokumenTanah as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $tanah->delete();

        return redirect()->route('tanah.index')
            ->with('success', 'Data Tanah berhasil dihapus.');
    }
}
