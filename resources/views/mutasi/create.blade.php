@extends('layouts.app')

@section('title', 'Catat Mutasi Kepemilikan')

@section('content')
    <div class="page-header">
        <h2 class="page-title">Catat Mutasi Kepemilikan</h2>
        <p class="page-subtitle">Daftarkan transaksi pemindahan hak atas kepemilikan kapling tanah.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <strong style="display: block; margin-bottom: 4px;">Terjadi Kesalahan:</strong>
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
            <h3 class="card-title">Formulir Transaksi Mutasi</h3>
        </div>

        <form action="{{ route('mutasi.store') }}" method="POST" enctype="multipart/form-data" style="padding: 24px;">
            @csrf

            <!-- Tanah Selection -->
            <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="tanah_id" class="form-label">Sertifikat Bidang Tanah</label>
                    <select id="tanah_id" name="tanah_id" class="form-input" style="color: white; background: #1a1a2e;" required>
                        <option value="">-- Pilih Bidang Tanah --</option>
                        @foreach ($tanahList as $t)
                            <option value="{{ $t->id }}" data-owner="{{ $t->pemilik->nama }}" {{ old('tanah_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->no_sertifikat }} - {{ $t->pemilik->nama }} ({{ number_format($t->luas, 0, ',', '.') }} m²)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Pemilik Aktif Saat Ini</label>
                    <input type="text" id="pemilik_lama_display" class="form-input" style="background: rgba(255, 255, 255, 0.05); color: var(--text-muted); cursor: not-allowed;" readonly placeholder="Pilih tanah dahulu...">
                </div>
            </div>

            <!-- Target Owner & Mutation Type -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="pemilik_baru_id" class="form-label">Pemilik Baru (Penerima Hak)</label>
                    <select id="pemilik_baru_id" name="pemilik_baru_id" class="form-input" style="color: white; background: #1a1a2e;" required>
                        <option value="">-- Pilih Pemilik Baru --</option>
                        @foreach ($pemilikList as $p)
                            <option value="{{ $p->id }}" {{ old('pemilik_baru_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }} - {{ $p->nik }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="jenis_mutasi" class="form-label">Jenis Transaksi Mutasi</label>
                    <select id="jenis_mutasi" name="jenis_mutasi" class="form-input" style="color: white; background: #1a1a2e;" required>
                        <option value="">-- Pilih Jenis Transaksi --</option>
                        <option value="jual_beli" {{ old('jenis_mutasi') == 'jual_beli' ? 'selected' : '' }}>Jual Beli</option>
                        <option value="waris" {{ old('jenis_mutasi') == 'waris' ? 'selected' : '' }}>Waris (Pewarisan)</option>
                        <option value="hibah" {{ old('jenis_mutasi') == 'hibah' ? 'selected' : '' }}>Hibah (Pemberian)</option>
                        <option value="tukar_guling" {{ old('jenis_mutasi') == 'tukar_guling' ? 'selected' : '' }}>Tukar Guling</option>
                    </select>
                </div>
            </div>

            <!-- Mutation Date & Document Upload -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="tanggal_mutasi" class="form-label">Tanggal Transaksi / Mutasi</label>
                    <input type="date" id="tanggal_mutasi" name="tanggal_mutasi" class="form-input" style="color-scheme: dark; color: white;" value="{{ old('tanggal_mutasi', now()->toDateString()) }}" required>
                </div>

                <div class="form-group">
                    <label for="dokumen_mutasi" class="form-label">
                        Unggah Dokumen Mutasi <span style="font-size: 11px; color: var(--text-muted);">(Akta / Surat Waris)</span>
                        <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px;">Format: PDF, JPG, PNG (Maks 5MB)</span>
                    </label>
                    <input type="file" id="dokumen_mutasi" name="dokumen_mutasi" class="form-input" style="padding-top: 10px; background-color: rgba(255,255,255,0.03); color: white; cursor: pointer;">
                </div>
            </div>

            <!-- Keterangan -->
            <div class="form-group" style="margin-bottom: 30px;">
                <label for="keterangan" class="form-label">Keterangan / Catatan Tambahan</label>
                <textarea id="keterangan" name="keterangan" class="form-input" rows="3" placeholder="Masukkan nomor Akta Notaris, nama pejabat pembuat akta tanah, atau alasan mutasi..." style="resize: vertical; min-height: 80px;">{{ old('keterangan') }}</textarea>
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 20px;">
                <a href="{{ route('mutasi.index') }}" class="btn btn-secondary" style="padding: 12px 24px;">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Catat Transaksi Mutasi
                </button>
            </div>
        </form>
    </div>

    <!-- JS script to reactively show current owner -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tanahSelect = document.getElementById('tanah_id');
            const pemilikLamaInput = document.getElementById('pemilik_lama_display');

            function updateOwnerDisplay() {
                const selectedOption = tanahSelect.options[tanahSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const ownerName = selectedOption.getAttribute('data-owner');
                    pemilikLamaInput.value = ownerName;
                } else {
                    pemilikLamaInput.value = '';
                }
            }

            tanahSelect.addEventListener('change', updateOwnerDisplay);
            // Run on load in case of validation failures restoring old input
            updateOwnerDisplay();
        });
    </script>
@endsection
