@extends('layouts.app')

@section('title', $tanah->exists ? 'Ubah Data Tanah' : 'Registrasi Tanah Baru')

@section('content')
    <div class="page-header">
        <h2 class="page-title">{{ $tanah->exists ? 'Ubah Data Tanah' : 'Registrasi Tanah Baru' }}</h2>
        <p class="page-subtitle">Isi data sertifikat kapling tanah dengan lengkap dan benar.</p>
    </div>

    <!-- Error Alert if Validation Fails -->
    @if ($errors->any())
        <div class="alert alert-error">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <strong style="display: block; margin-bottom: 4px;">Terjadi Kesalahan Validasi:</strong>
                <ul style="margin-left: 16px; font-size: 13px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header">
            <h3 class="card-title">Formulir Registrasi Sertifikat</h3>
        </div>

        <form action="{{ $tanah->exists ? route('tanah.update', $tanah->id) : route('tanah.store') }}" method="POST" enctype="multipart/form-data" style="padding: 24px;">
            @csrf
            @if ($tanah->exists)
                @method('PUT')
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="no_sertifikat" class="form-label">No. Sertifikat / Persil</label>
                    <input type="text" id="no_sertifikat" name="no_sertifikat" class="form-input" placeholder="Masukkan nomor sertifikat resmi..." value="{{ old('no_sertifikat', $tanah->no_sertifikat) }}" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="jenis_hak_id" class="form-label">Jenis Hak Tanah</label>
                    <select id="jenis_hak_id" name="jenis_hak_id" class="form-input" style="background-color: rgba(255,255,255,0.03); color: white;" required>
                        <option value="" style="background-color: #1a1a2e; color: var(--text-muted);">Pilih Jenis Hak...</option>
                        @foreach ($jenisHakList as $item)
                            <option value="{{ $item->id }}" style="background-color: #1a1a2e;" {{ old('jenis_hak_id', $tanah->jenis_hak_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->kode }} - {{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Village Letter C and Persil -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="no_letter_c" class="form-label">No. Buku Letter C <span style="font-size: 11px; color: var(--text-muted);">(Opsional)</span></label>
                    <input type="text" id="no_letter_c" name="no_letter_c" class="form-input" placeholder="Contoh: C 124..." value="{{ old('no_letter_c', $tanah->no_letter_c) }}" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="no_persil" class="form-label">No. Persil <span style="font-size: 11px; color: var(--text-muted);">(Opsional)</span></label>
                    <input type="text" id="no_persil" name="no_persil" class="form-input" placeholder="Contoh: Persil 45..." value="{{ old('no_persil', $tanah->no_persil) }}" autocomplete="off">
                </div>
            </div>

            <!-- Village Klas Tanah and Status Bengkok -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="klas_tanah" class="form-label">Klasifikasi Tanah <span style="font-size: 11px; color: var(--text-muted);">(Opsional, Contoh: S.I, D.II)</span></label>
                    <input type="text" id="klas_tanah" name="klas_tanah" class="form-input" placeholder="Masukkan kelas tanah adat..." value="{{ old('klas_tanah', $tanah->klas_tanah) }}" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="status_bengkok" class="form-label">Status Bengkok / Kas Desa <span style="font-size: 11px; color: var(--text-muted);">(Opsional)</span></label>
                    <select id="status_bengkok" name="status_bengkok" class="form-input" style="background-color: rgba(255,255,255,0.03); color: white;">
                        <option value="" style="background-color: #1a1a2e; color: var(--text-muted);">Bukan Bengkok / Umum</option>
                        <option value="Bengkok Kepala Desa" style="background-color: #1a1a2e;" {{ old('status_bengkok', $tanah->status_bengkok) == 'Bengkok Kepala Desa' ? 'selected' : '' }}>Bengkok Kepala Desa</option>
                        <option value="Bengkok Sekretaris Desa" style="background-color: #1a1a2e;" {{ old('status_bengkok', $tanah->status_bengkok) == 'Bengkok Sekretaris Desa' ? 'selected' : '' }}>Bengkok Sekretaris Desa</option>
                        <option value="Bengkok Perangkat Desa" style="background-color: #1a1a2e;" {{ old('status_bengkok', $tanah->status_bengkok) == 'Bengkok Perangkat Desa' ? 'selected' : '' }}>Bengkok Perangkat Desa</option>
                        <option value="Tanah Kas Desa (TKD)" style="background-color: #1a1a2e;" {{ old('status_bengkok', $tanah->status_bengkok) == 'Tanah Kas Desa (TKD)' ? 'selected' : '' }}>Tanah Kas Desa (TKD)</option>
                        <option value="Tanah Wakaf / Fasilitas Umum" style="background-color: #1a1a2e;" {{ old('status_bengkok', $tanah->status_bengkok) == 'Tanah Wakaf / Fasilitas Umum' ? 'selected' : '' }}>Tanah Wakaf / Fasilitas Umum</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="luas" class="form-label">Luas Tanah (m²)</label>
                    <input type="number" step="0.01" id="luas" name="luas" class="form-input" placeholder="Masukkan luas tanah dalam meter persegi..." value="{{ old('luas', $tanah->luas) }}" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="wilayah_id" class="form-label">Lokasi Wilayah (Desa/Kelurahan - Kecamatan)</label>
                    <select id="wilayah_id" name="wilayah_id" class="form-input" style="background-color: rgba(255,255,255,0.03); color: white;" required>
                        <option value="" style="background-color: #1a1a2e; color: var(--text-muted);">Pilih Desa/Kecamatan...</option>
                        @foreach ($wilayahList as $item)
                            <option value="{{ $item->id }}" style="background-color: #1a1a2e;" {{ old('wilayah_id', $tanah->wilayah_id) == $item->id ? 'selected' : '' }}>
                                Desa {{ $item->nama_desa }} (Kec. {{ $item->nama_kecamatan }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="alamat" class="form-label">Alamat / Detail Lokasi</label>
                <textarea id="alamat" name="alamat" class="form-input" rows="3" placeholder="Masukkan alamat lengkap tanah (RT/RW, dusun, jalan)..." required style="resize: vertical; min-height: 80px;">{{ old('alamat', $tanah->alamat) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="status_tanah_id" class="form-label">Status Keaktifan Tanah</label>
                    <select id="status_tanah_id" name="status_tanah_id" class="form-input" style="background-color: rgba(255,255,255,0.03); color: white;" required>
                        <option value="" style="background-color: #1a1a2e; color: var(--text-muted);">Pilih Status...</option>
                        @foreach ($statusTanahList as $item)
                            <option value="{{ $item->id }}" style="background-color: #1a1a2e;" {{ old('status_tanah_id', $tanah->status_tanah_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="pemilik_id" class="form-label">Pemilik Saat Ini</label>
                    <select id="pemilik_id" name="pemilik_id" class="form-input" style="background-color: rgba(255,255,255,0.03); color: white;" required>
                        <option value="" style="background-color: #1a1a2e; color: var(--text-muted);">Pilih Pemilik...</option>
                        @foreach ($pemilikList as $item)
                            <option value="{{ $item->id }}" style="background-color: #1a1a2e;" {{ old('pemilik_id', $tanah->pemilik_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->nama }} (NIK: {{ $item->nik }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="latitude" class="form-label">Garis Lintang (Latitude) <span style="font-size: 11px; color: var(--text-muted);">(Opsional)</span></label>
                    <input type="text" id="latitude" name="latitude" class="form-input" placeholder="Contoh: -7.2654" value="{{ old('latitude', $tanah->latitude) }}" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="longitude" class="form-label">Garis Bujur (Longitude) <span style="font-size: 11px; color: var(--text-muted);">(Opsional)</span></label>
                    <input type="text" id="longitude" name="longitude" class="form-input" placeholder="Contoh: 112.7431" value="{{ old('longitude', $tanah->longitude) }}" autocomplete="off">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div class="form-group">
                    <label for="dokumen_sertifikat" class="form-label">
                        Scan Dokumen Sertifikat
                        <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px;">Format: PDF, JPG, PNG (Maks 5MB)</span>
                    </label>
                    <input type="file" id="dokumen_sertifikat" name="dokumen_sertifikat" class="form-input" style="padding-top: 10px; background-color: rgba(255,255,255,0.03); color: white; cursor: pointer;">
                    @if ($tanah->exists && $doc = $tanah->dokumenTanah->where('nama_dokumen', 'Scan Sertifikat')->first())
                        <div style="margin-top: 8px; font-size: 12px;">
                            <span style="color: var(--text-muted);">File terlampir:</span>
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" style="color: #60a5fa; text-decoration: underline; margin-left: 4px;">Lihat Dokumen</a>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="foto_lokasi" class="form-label">
                        Foto Lokasi Tanah
                        <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px;">Format: JPG, PNG (Maks 5MB)</span>
                    </label>
                    <input type="file" id="foto_lokasi" name="foto_lokasi" class="form-input" style="padding-top: 10px; background-color: rgba(255,255,255,0.03); color: white; cursor: pointer;">
                    @if ($tanah->exists && $doc = $tanah->dokumenTanah->where('nama_dokumen', 'Foto Lokasi')->first())
                        <div style="margin-top: 8px; font-size: 12px;">
                            <span style="color: var(--text-muted);">File terlampir:</span>
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" style="color: #60a5fa; text-decoration: underline; margin-left: 4px;">Lihat Foto</a>
                        </div>
                    @endif
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 20px;">
                <a href="{{ route('tanah.index') }}" class="btn btn-secondary" style="padding: 12px 24px;">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
@endsection
