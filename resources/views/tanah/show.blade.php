@extends('layouts.app')

@section('title', 'Detail Tanah - ' . $tanah->no_sertifikat)

@section('content')
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
            align-items: start;
        }

        /* Timeline Styles */
        .timeline {
            position: relative;
            border-left: 2px solid rgba(255, 255, 255, 0.08);
            padding-left: 24px;
            margin-left: 12px;
            margin-top: 16px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 28px;
        }
        .timeline-item:last-child {
            margin-bottom: 0;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -33px;
            top: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #1a1a2e;
            border: 3px solid #3b82f6;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }
        .timeline-item.registration::before {
            border-color: #10b981;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        }
        .timeline-date {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .timeline-title {
            font-size: 14px;
            font-weight: 600;
            color: white;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .timeline-body {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.04);
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 6px;
        }

        /* Document list styles */
        .doc-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 16px;
        }
        .doc-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.2s ease;
        }
        .doc-item:hover {
            border-color: rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.04);
        }
        .doc-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .doc-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .doc-icon.image {
            background: rgba(168, 85, 247, 0.15);
            color: #c084fc;
        }
    </style>

    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 class="page-title">Detail Registrasi Tanah</h2>
            <p class="page-subtitle">Sertifikat No. {{ $tanah->no_sertifikat }}</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('tanah.index') }}" class="btn btn-secondary">
                Kembali
            </a>
            <a href="{{ route('tanah.edit', $tanah->id) }}" class="btn btn-primary" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                Ubah Data
            </a>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Left Side: Basic Parameters & Info -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Informasi Utama -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Utama Tanah</h3>
                </div>
                <div style="padding: 24px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px; width: 220px;">No. Sertifikat / Persil</td>
                            <td style="padding: 12px 0; color: white; font-weight: 600; font-size: 14px;">{{ $tanah->no_sertifikat }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px;">Buku Letter C</td>
                            <td style="padding: 12px 0; color: white; font-size: 14px;">{{ $tanah->no_letter_c ?: '-' }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px;">Nomor Persil</td>
                            <td style="padding: 12px 0; color: white; font-size: 14px;">{{ $tanah->no_persil ?: '-' }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px;">Klasifikasi Tanah Adat</td>
                            <td style="padding: 12px 0; color: white; font-size: 14px;">{{ $tanah->klas_tanah ?: '-' }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px;">Status Bengkok / Kas Desa</td>
                            <td style="padding: 12px 0; color: white; font-size: 14px;">
                                @if($tanah->status_bengkok)
                                    <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #c084fc; border: 1px solid rgba(139, 92, 246, 0.3);">{{ $tanah->status_bengkok }}</span>
                                @else
                                    <span style="color: var(--text-muted);">Bukan Bengkok (Umum)</span>
                                @endif
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px;">Jenis Hak</td>
                            <td style="padding: 12px 0; color: white; font-size: 14px;">
                                <span class="badge badge-accent" style="margin-right: 8px;">{{ $tanah->jenisHak->kode }}</span>
                                {{ $tanah->jenisHak->nama }}
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px;">Luas Kapling</td>
                            <td style="padding: 12px 0; color: white; font-weight: 500; font-size: 14px;">{{ number_format($tanah->luas, 0, ',', '.') }} m²</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px;">Wilayah Administrasi</td>
                            <td style="padding: 12px 0; color: white; font-size: 14px;">
                                Desa {{ $tanah->wilayah->nama_desa }}, Kecamatan {{ $tanah->wilayah->nama_kecamatan }}
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px;">Alamat Fisik</td>
                            <td style="padding: 12px 0; color: white; font-size: 14px; line-height: 1.5;">{{ $tanah->alamat }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px;">Status Keaktifan</td>
                            <td style="padding: 12px 0; color: white; font-size: 14px;">
                                @if($tanah->statusTanah->nama == 'Aktif')
                                    <span class="badge badge-success">{{ $tanah->statusTanah->nama }}</span>
                                @elseif($tanah->statusTanah->nama == 'Sengketa')
                                    <span class="badge badge-danger">{{ $tanah->statusTanah->nama }}</span>
                                @elseif($tanah->statusTanah->nama == 'Dijual')
                                    <span class="badge badge-accent">{{ $tanah->statusTanah->nama }}</span>
                                @else
                                    <span class="badge" style="background: rgba(255, 255, 255, 0.08); color: var(--text-muted); border: 1px solid var(--border);">{{ $tanah->statusTanah->nama }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px;">Pemilik Aktif Saat Ini</td>
                            <td style="padding: 12px 0; color: white; font-weight: 500; font-size: 14px;">
                                {{ $tanah->pemilik->nama }}
                                <div style="font-size: 12px; color: var(--text-muted); font-weight: normal; margin-top: 2px;">NIK: {{ $tanah->pemilik->nik }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 14px;">Koordinat Geografis</td>
                            <td style="padding: 12px 0; color: white; font-size: 14px;">
                                @if ($tanah->latitude && $tanah->longitude)
                                    <code>{{ $tanah->latitude }}, {{ $tanah->longitude }}</code>
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $tanah->latitude }},{{ $tanah->longitude }}" target="_blank" class="btn btn-secondary btn-sm" style="margin-left: 12px; display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; font-size: 11px;">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Buka Google Maps
                                    </a>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Tidak ada koordinat terdaftar.</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Lampiran Dokumen -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lampiran Dokumen Digital</h3>
                </div>
                <div style="padding: 24px;">
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">Berikut adalah pindaian file digital yang dilampirkan pada kapling tanah ini:</p>
                    <div class="doc-list">
                        @forelse ($tanah->dokumenTanah as $doc)
                            @php
                                $isPdf = str_ends_with(strtolower($doc->file_path), '.pdf');
                            @endphp
                            <div class="doc-item">
                                <div class="doc-info">
                                    <div class="doc-icon {{ $isPdf ? '' : 'image' }}">
                                        @if ($isPdf)
                                            <!-- PDF Icon -->
                                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        @else
                                            <!-- Image Icon -->
                                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: white; font-size: 13px;">{{ $doc->nama_dokumen }}</div>
                                        <div style="font-size: 11px; color: var(--text-muted);">Diunggah pada: {{ $doc->uploaded_at ? \Carbon\Carbon::parse($doc->uploaded_at)->translatedFormat('d F Y H:i') : '-' }}</div>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Unduh File
                                </a>
                            </div>
                        @empty
                            <div style="text-align: center; color: var(--text-muted); font-style: italic; font-size: 13px; padding: 20px 0;">
                                Tidak ada lampiran dokumen digital untuk tanah ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Histori Kepemilikan (Timeline) -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Histori Mutasi Kepemilikan</h3>
            </div>
            <div style="padding: 24px;">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Kronologi pelacakan mutasi dan pendaftaran kepemilikan kapling tanah ini:</p>

                <div class="timeline">
                    @forelse ($tanah->riwayatKepemilikan as $history)
                        @php
                            $isReg = $history->jenis_mutasi === 'pendaftaran';
                        @endphp
                        <div class="timeline-item {{ $isReg ? 'registration' : '' }}">
                            <div class="timeline-date">
                                {{ \Carbon\Carbon::parse($history->tanggal_mutasi)->translatedFormat('d M Y') }}
                            </div>
                            <div class="timeline-title">
                                @if ($isReg)
                                    <span class="badge badge-success" style="font-size: 10px; padding: 2px 6px;">Pendaftaran</span>
                                    <span>Pendaftaran Awal</span>
                                @else
                                    <span class="badge badge-accent" style="font-size: 10px; padding: 2px 6px;">Mutasi</span>
                                    <span>Mutasi Hak Milik</span>
                                @endif
                            </div>
                            <div class="timeline-body">
                                @if ($isReg)
                                    <div>Pendaftaran pertama atas nama: <strong>{{ $history->pemilikBaru->nama }}</strong></div>
                                @else
                                    <div style="margin-bottom: 4px;">Pemilik Lama: <span style="text-decoration: line-through; opacity: 0.8;">{{ $history->pemilikLama->nama }}</span></div>
                                    <div>Pemilik Baru: <strong>{{ $history->pemilikBaru->nama }}</strong></div>
                                @endif

                                @if ($history->keterangan)
                                    <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed rgba(255,255,255,0.04); font-size: 12px; color: var(--text-muted); font-style: italic;">
                                        &ldquo;{{ $history->keterangan }}&rdquo;
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-muted); font-style: italic; font-size: 13px; padding: 20px 0;">
                            Tidak ada histori kepemilikan yang terekam.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
