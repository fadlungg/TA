@extends('layouts.app')

@section('title', 'Detail Pemilik - ' . $pemilik->nama)

@section('content')
    <style>
        .profile-grid {
            display: grid;
            grid-template-columns: 0.8fr 1.2fr;
            gap: 24px;
            align-items: start;
        }

        .ktp-preview-container {
            width: 100%;
            height: 200px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.02);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-top: 16px;
        }

        .ktp-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .ktp-preview-container:hover .ktp-image {
            transform: scale(1.05);
        }

        .ktp-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 13px;
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
            margin-bottom: 24px;
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
        .timeline-item.sell::before {
            border-color: #ef4444;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
        }
        .timeline-item.buy::before {
            border-color: #10b981;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        }
        .timeline-date {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .timeline-title {
            font-size: 13px;
            font-weight: 600;
            color: white;
        }
        .timeline-body {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.04);
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 6px;
        }
    </style>

    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 class="page-title">Detail Pemilik</h2>
            <p class="page-subtitle">Profil dan data kepemilikan tanah {{ $pemilik->nama }}.</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('pemilik.index') }}" class="btn btn-secondary">
                Kembali
            </a>
            <a href="{{ route('pemilik.edit', $pemilik->id) }}" class="btn btn-primary" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                Ubah Profil
            </a>
        </div>
    </div>

    <div class="profile-grid">
        <!-- Left Side: Profile Details & KTP -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profil Pemilik</h3>
                </div>
                <div style="padding: 24px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 13px; width: 140px;">Nama Lengkap</td>
                            <td style="padding: 12px 0; color: white; font-weight: 600; font-size: 13px;">{{ $pemilik->nama }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 13px;">NIK</td>
                            <td style="padding: 12px 0; color: white; font-size: 13px;"><code>{{ $pemilik->nik }}</code></td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 13px;">Tempat/Tgl Lahir</td>
                            <td style="padding: 12px 0; color: white; font-size: 13px;">
                                {{ $pemilik->tempat_lahir }}, {{ $pemilik->tanggal_lahir ? $pemilik->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 13px;">No. Telepon / HP</td>
                            <td style="padding: 12px 0; color: white; font-size: 13px;">{{ $pemilik->no_hp }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 13px;">Email</td>
                            <td style="padding: 12px 0; color: white; font-size: 13px;">{{ $pemilik->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 0; color: var(--text-muted); font-size: 13px; vertical-align: top;">Alamat</td>
                            <td style="padding: 12px 0; color: white; font-size: 13px; line-height: 1.5; white-space: pre-line;">{{ $pemilik->alamat }}</td>
                        </tr>
                    </table>

                    <div style="margin-top: 24px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 16px;">
                        <span class="form-label" style="margin-bottom: 8px; display: block;">Dokumen Foto KTP</span>
                        <div class="ktp-preview-container">
                            @if($pemilik->foto_ktp)
                                <a href="{{ asset('storage/' . $pemilik->foto_ktp) }}" target="_blank" style="width: 100%; height: 100%;">
                                    <img src="{{ asset('storage/' . $pemilik->foto_ktp) }}" alt="Foto KTP {{ $pemilik->nama }}" class="ktp-image">
                                </a>
                            @else
                                <div class="ktp-placeholder">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    </svg>
                                    <span>Belum ada foto KTP terunggah</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Bidang Tanah Aktif & Riwayat Tanah -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Bidang Tanah Aktif -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bidang Tanah yang Dimiliki (Aktif)</h3>
                    <span class="badge badge-success">{{ $tanahAktif->count() }} Bidang</span>
                </div>
                <div style="padding: 24px;">
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>No. Sertifikat</th>
                                    <th>Luas</th>
                                    <th>Wilayah</th>
                                    <th>Jenis Hak</th>
                                    <th>Status</th>
                                    <th style="width: 80px; text-align: center;">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tanahAktif as $t)
                                    <tr>
                                        <td style="font-weight: 600; color: white;">{{ $t->no_sertifikat }}</td>
                                        <td>{{ number_format($t->luas, 0, ',', '.') }} m²</td>
                                        <td>Desa {{ $t->wilayah->nama_desa }}</td>
                                        <td><span class="badge badge-accent">{{ $t->jenisHak->kode }}</span></td>
                                        <td>
                                            @if($t->statusTanah->nama == 'Aktif')
                                                <span class="badge badge-success">{{ $t->statusTanah->nama }}</span>
                                            @elseif($t->statusTanah->nama == 'Sengketa')
                                                <span class="badge badge-danger">{{ $t->statusTanah->nama }}</span>
                                            @else
                                                <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--border);">{{ $t->statusTanah->nama }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <a href="{{ route('tanah.show', $t->id) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px;">
                                                    Buka
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--text-muted); font-style: italic; padding: 24px;">
                                            Tidak ada bidang tanah yang aktif dimiliki saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Riwayat Mutasi Tanah (Timeline Pernah Dimiliki) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Mutasi Tanah (Pernah Dimiliki)</h3>
                </div>
                <div style="padding: 24px;">
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">Catatan kronologis keterlibatan dalam mutasi kepemilikan tanah:</p>
                    <div class="timeline">
                        @forelse ($riwayatTanah as $h)
                            @php
                                $isBuyer = $h->pemilik_baru_id == $pemilik->id;
                                $isReg = $h->jenis_mutasi === 'pendaftaran';
                            @endphp
                            <div class="timeline-item {{ $isReg ? 'buy' : ($isBuyer ? 'buy' : 'sell') }}">
                                <div class="timeline-date">
                                    {{ \Carbon\Carbon::parse($h->tanggal_mutasi)->translatedFormat('d M Y') }}
                                </div>
                                <div class="timeline-title" style="display: flex; align-items: center; justify-content: space-between;">
                                    <span style="font-weight: 600; color: white;">
                                        @if ($isReg)
                                            Pendaftaran Sertifikat Baru
                                        @elseif ($isBuyer)
                                            Menerima Hak Milik (Membeli/Waris)
                                        @else
                                            Pelepasan Hak Milik (Menjual/Waris)
                                        @endif
                                    </span>
                                    <span style="font-size: 11px; color: var(--text-muted);">Sertifikat: {{ $h->tanah->no_sertifikat }}</span>
                                </div>
                                <div class="timeline-body">
                                    @if ($isReg)
                                        Pendaftaran kapling tanah seluas <strong>{{ number_format($h->tanah->luas, 0, ',', '.') }} m²</strong> di Desa {{ $h->tanah->wilayah->nama_desa }} untuk pertama kali.
                                    @elseif ($isBuyer)
                                        Menerima kepemilikan kapling tanah seluas <strong>{{ number_format($h->tanah->luas, 0, ',', '.') }} m²</strong> di Desa {{ $h->tanah->wilayah->nama_desa }} @if($h->pemilikLama) dari <strong>{{ $h->pemilikLama->nama }}</strong> @endif.
                                    @else
                                        Melepaskan kepemilikan kapling tanah seluas <strong>{{ number_format($h->tanah->luas, 0, ',', '.') }} m²</strong> di Desa {{ $h->tanah->wilayah->nama_desa }} kepada <strong>{{ $h->pemilikBaru->nama }}</strong>.
                                    @endif

                                    @if ($h->keterangan)
                                        <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed rgba(255,255,255,0.04); font-size: 11px; color: var(--text-muted); font-style: italic;">
                                            &ldquo;{{ $h->keterangan }}&rdquo;
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; color: var(--text-muted); font-style: italic; font-size: 13px; padding: 12px 0;">
                                Belum ada riwayat mutasi kepemilikan tanah terdaftar.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
