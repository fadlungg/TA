@extends('layouts.app')

@section('title', $pemilik->exists ? 'Ubah Data Pemilik' : 'Tambah Pemilik Baru')

@section('content')
    <div class="page-header">
        <h2 class="page-title">{{ $pemilik->exists ? 'Ubah Data Pemilik' : 'Tambah Pemilik Baru' }}</h2>
        <p class="page-subtitle">Isi data identitas pemilik kapling tanah dengan lengkap dan benar.</p>
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

    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h3 class="card-title">Formulir Profil Pemilik</h3>
        </div>

        <form action="{{ $pemilik->exists ? route('pemilik.update', $pemilik->id) : route('pemilik.store') }}" method="POST" enctype="multipart/form-data" style="padding: 24px;">
            @csrf
            @if ($pemilik->exists)
                @method('PUT')
            @endif

            <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-input" placeholder="Masukkan nama lengkap sesuai KTP..." value="{{ old('nama', $pemilik->nama) }}" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="nik" class="form-label">Nomor Induk Kependudukan (NIK)</label>
                    <input type="text" id="nik" name="nik" class="form-input" placeholder="Masukkan 16 digit NIK..." value="{{ old('nik', $pemilik->nik) }}" required minlength="16" maxlength="16" autocomplete="off" style="font-family: monospace;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-input" placeholder="Kota/Kabupaten kelahiran..." value="{{ old('tempat_lahir', $pemilik->tempat_lahir) }}" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-input" style="color-scheme: dark; color: white;" value="{{ old('tanggal_lahir', $pemilik->tanggal_lahir ? $pemilik->tanggal_lahir->format('Y-m-d') : '') }}" required>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="alamat" class="form-label">Alamat Lengkap</label>
                <textarea id="alamat" name="alamat" class="form-input" rows="3" placeholder="Masukkan alamat lengkap domisili saat ini..." required style="resize: vertical; min-height: 80px;">{{ old('alamat', $pemilik->alamat) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="no_hp" class="form-label">Nomor Telepon / HP</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-input" placeholder="Contoh: 0812XXXXXXXX..." value="{{ old('no_hp', $pemilik->no_hp) }}" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email <span style="font-size: 11px; color: var(--text-muted);">(Opsional)</span></label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Contoh: pemilik@email.com..." value="{{ old('email', $pemilik->email) }}" autocomplete="off">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="foto_ktp" class="form-label">
                    Unggah Foto/Scan KTP
                    <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px;">Format: JPG, PNG (Maks 5MB)</span>
                </label>
                <input type="file" id="foto_ktp" name="foto_ktp" class="form-input" style="padding-top: 10px; background-color: rgba(255,255,255,0.03); color: white; cursor: pointer;">
                @if ($pemilik->exists && $pemilik->foto_ktp)
                    <div style="margin-top: 8px; font-size: 12px; display: flex; align-items: center; gap: 8px;">
                        <span style="color: var(--text-muted);">Foto KTP Terunggah:</span>
                        <a href="{{ asset('storage/' . $pemilik->foto_ktp) }}" target="_blank" style="color: #60a5fa; text-decoration: underline;">Lihat KTP</a>
                    </div>
                @endif
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 20px;">
                <a href="{{ route('pemilik.index') }}" class="btn btn-secondary" style="padding: 12px 24px;">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>
@endsection
