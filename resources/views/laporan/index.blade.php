@extends('layouts.app')

@section('title', 'Laporan & Analisis')

@section('content')
    <div class="page-header">
        <h2 class="page-title">Laporan & Analisis</h2>
        <p class="page-subtitle font-normal">Unduh data rekapitulasi, bidang tanah, serta log mutasi kepemilikan.</p>
    </div>

    <!-- Filter Card -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
        </div>
        <form action="{{ route('laporan.index') }}" method="GET" id="filter-form" style="padding: 20px;">
            <!-- Hidden tab state to preserve active tab after filtering -->
            <input type="hidden" name="tab" id="tab-state" value="{{ request('tab', 'tanah') }}">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: flex-end;">
                <!-- Filter Wilayah -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="wilayah_id" class="form-label">Wilayah (Desa/Kec)</label>
                    <select id="wilayah_id" name="wilayah_id" class="form-input" style="color: white; background: #1a1a2e;">
                        <option value="">Semua Wilayah</option>
                        @foreach ($wilayahList as $w)
                            <option value="{{ $w->id }}" {{ request('wilayah_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->nama_kecamatan }} - {{ $w->nama_desa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Jenis Hak -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="jenis_hak_id" class="form-label">Jenis Hak</label>
                    <select id="jenis_hak_id" name="jenis_hak_id" class="form-input" style="color: white; background: #1a1a2e;">
                        <option value="">Semua Hak</option>
                        @foreach ($jenisHakList as $j)
                            <option value="{{ $j->id }}" {{ request('jenis_hak_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->kode }} - {{ $j->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status (Only for Tanah tab) -->
                <div class="form-group" id="filter-status-group" style="margin-bottom: 0;">
                    <label for="status_tanah_id" class="form-label">Status Tanah</label>
                    <select id="status_tanah_id" name="status_tanah_id" class="form-input" style="color: white; background: #1a1a2e;">
                        <option value="">Semua Status</option>
                        @foreach ($statusTanahList as $s)
                            <option value="{{ $s->id }}" {{ request('status_tanah_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date range filters (Only for Mutasi tab) -->
                <div class="form-group date-filter-group" style="margin-bottom: 0; display: none;">
                    <label for="start_date" class="form-label">Mulai Tanggal</label>
                    <input type="date" id="start_date" name="start_date" class="form-input" style="color-scheme: dark; color: white;" value="{{ $start_date }}">
                </div>

                <div class="form-group date-filter-group" style="margin-bottom: 0; display: none;">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" class="form-input" style="color-scheme: dark; color: white;" value="{{ $end_date }}">
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px 16px;">
                        Filter
                    </button>
                    <a href="{{ route('laporan.index') }}" class="btn btn-secondary" style="padding: 12px 16px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabs Navigation -->
    <div style="display: flex; border-bottom: 1px solid var(--border); margin-bottom: 24px; gap: 8px;">
        <button type="button" class="tab-btn active" data-target="tab-tanah" onclick="switchTab('tanah')">
            Laporan Bidang Tanah
        </button>
        <button type="button" class="tab-btn" data-target="tab-rekap" onclick="switchTab('rekap')">
            Rekap Luas Per Wilayah
        </button>
        <button type="button" class="tab-btn" data-target="tab-mutasi" onclick="switchTab('mutasi')">
            Laporan Mutasi Periode
        </button>
    </div>

    <!-- Tab 1: Bidang Tanah -->
    <div id="tab-tanah" class="tab-content">
        <!-- Stats Summary -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="card" style="padding: 20px; display: flex; align-items: center; gap: 16px; background: linear-gradient(135deg, rgba(96, 165, 250, 0.15), rgba(96, 165, 250, 0.05)); border: 1px solid rgba(96, 165, 250, 0.2);">
                <div style="padding: 12px; background: rgba(96, 165, 250, 0.2); border-radius: 12px; color: #60a5fa;">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <div>
                    <div style="font-size: 13px; color: var(--text-muted);">Total Bidang Tanah (Tersaring)</div>
                    <div style="font-size: 28px; font-weight: 700; color: white; margin-top: 4px;">{{ number_format($tanahData->count(), 0, ',', '.') }} Bidang</div>
                </div>
            </div>

            <div class="card" style="padding: 20px; display: flex; align-items: center; gap: 16px; background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.05)); border: 1px solid rgba(16, 185, 129, 0.2);">
                <div style="padding: 12px; background: rgba(16, 185, 129, 0.2); border-radius: 12px; color: #10b981;">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                </div>
                <div>
                    <div style="font-size: 13px; color: var(--text-muted);">Total Luas Tanah (Tersaring)</div>
                    <div style="font-size: 28px; font-weight: 700; color: white; margin-top: 4px;">{{ number_format($tanahData->sum('luas'), 0, ',', '.') }} m²</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Bidang Tanah</h3>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('laporan.export-tanah', request()->query()) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Ekspor Excel (CSV)
                    </a>
                    <a href="{{ route('laporan.print-tanah', request()->query()) }}" target="_blank" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>No. Sertifikat</th>
                            <th>Luas (m²)</th>
                            <th>Alamat/Lokasi</th>
                            <th>Kecamatan</th>
                            <th>Desa</th>
                            <th>Jenis Hak</th>
                            <th>Status</th>
                            <th>Pemilik Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tanahData as $idx => $t)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td style="font-weight: 600; color: white;">{{ $t->no_sertifikat }}</td>
                                <td>{{ number_format($t->luas, 0, ',', '.') }} m²</td>
                                <td>{{ $t->alamat }}</td>
                                <td>{{ $t->wilayah->nama_kecamatan }}</td>
                                <td>{{ $t->wilayah->nama_desa }}</td>
                                <td>{{ $t->jenisHak->kode }}</td>
                                <td>
                                    @if ($t->statusTanah->nama === 'Aktif')
                                        <span class="badge badge-success">Aktif</span>
                                    @elseif ($t->statusTanah->nama === 'Sengketa')
                                        <span class="badge badge-danger">Sengketa</span>
                                    @elseif ($t->statusTanah->nama === 'Dijual')
                                        <span class="badge badge-warning">Dijual</span>
                                    @else
                                        <span class="badge">{{ $t->statusTanah->nama }}</span>
                                    @endif
                                </td>
                                <td style="font-weight: 500;">{{ $t->pemilik->nama }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                    Tidak ada data bidang tanah yang memenuhi filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 2: Rekap Luas per Wilayah -->
    <div id="tab-rekap" class="tab-content" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rekapitulasi Luas Bidang per Desa/Wilayah</h3>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('laporan.export-rekap') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Ekspor Excel (CSV)
                    </a>
                    <a href="{{ route('laporan.print-rekap') }}" target="_blank" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kecamatan</th>
                            <th>Desa</th>
                            <th>Jumlah Bidang Tanah</th>
                            <th>Total Luas (m²)</th>
                            <th>Persentase Luas (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekapData as $idx => $r)
                            @php
                                $persentase = $totalLuasOverall > 0 ? ($r->total_luas / $totalLuasOverall) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td style="font-weight: 500; color: white;">{{ $r->nama_kecamatan }}</td>
                                <td style="font-weight: 500; color: white;">{{ $r->nama_desa }}</td>
                                <td>{{ number_format($r->jumlah_bidang, 0, ',', '.') }} Bidang</td>
                                <td>{{ number_format($r->total_luas, 0, ',', '.') }} m²</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="flex: 1; height: 6px; background: rgba(255,255,255,0.05); border-radius: 3px; overflow: hidden; max-width: 100px;">
                                            <div style="height: 100%; width: {{ $persentase }}%; background: #60a5fa; border-radius: 3px;"></div>
                                        </div>
                                        <span>{{ number_format($persentase, 2, ',', '.') }} %</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                    Belum ada data wilayah atau bidang tanah terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 3: Laporan Mutasi Periode -->
    <div id="tab-mutasi" class="tab-content" style="display: none;">
        <!-- Stats Summary -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="card" style="padding: 20px; display: flex; align-items: center; gap: 16px; background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.05)); border: 1px solid rgba(245, 158, 11, 0.2);">
                <div style="padding: 12px; background: rgba(245, 158, 11, 0.2); border-radius: 12px; color: #f59e0b;">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <div>
                    <div style="font-size: 13px; color: var(--text-muted);">Total Mutasi Tercatat (Periode)</div>
                    <div style="font-size: 28px; font-weight: 700; color: white; margin-top: 4px;">{{ number_format($mutasiData->count(), 0, ',', '.') }} Transaksi</div>
                </div>
            </div>

            <div class="card" style="padding: 20px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; align-items: center;">
                <div style="text-align: center; border-right: 1px solid var(--border);">
                    <div style="font-size: 11px; color: var(--text-muted);">Jual Beli</div>
                    <div style="font-size: 20px; font-weight: 700; color: #10b981; margin-top: 4px;">{{ $mutasiData->where('jenis_mutasi', 'jual_beli')->count() }}</div>
                </div>
                <div style="text-align: center; border-right: 1px solid var(--border);">
                    <div style="font-size: 11px; color: var(--text-muted);">Waris</div>
                    <div style="font-size: 20px; font-weight: 700; color: #8b5cf6; margin-top: 4px;">{{ $mutasiData->where('jenis_mutasi', 'waris')->count() }}</div>
                </div>
                <div style="text-align: center; border-right: 1px solid var(--border);">
                    <div style="font-size: 11px; color: var(--text-muted);">Hibah</div>
                    <div style="font-size: 20px; font-weight: 700; color: #f59e0b; margin-top: 4px;">{{ $mutasiData->where('jenis_mutasi', 'hibah')->count() }}</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 11px; color: var(--text-muted);">Tukar Guling</div>
                    <div style="font-size: 20px; font-weight: 700; color: #ef4444; margin-top: 4px;">{{ $mutasiData->where('jenis_mutasi', 'tukar_guling')->count() }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Log Transaksi Mutasi Tanah</h3>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('laporan.export-mutasi', request()->query()) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Ekspor Excel (CSV)
                    </a>
                    <a href="{{ route('laporan.print-mutasi', request()->query()) }}" target="_blank" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>No. Sertifikat</th>
                            <th>Pemilik Sebelumnya</th>
                            <th>Pemilik Baru</th>
                            <th>Jenis Mutasi</th>
                            <th>Tanggal Mutasi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mutasiData as $idx => $m)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td style="font-weight: 600; color: white;">{{ $m->tanah->no_sertifikat }}</td>
                                <td>
                                    @if ($m->pemilikLama)
                                        {{ $m->pemilikLama->nama }}
                                    @else
                                        <span style="color: var(--text-muted); font-style: italic;">Pendaftaran Awal</span>
                                    @endif
                                </td>
                                <td style="font-weight: 500; color: white;">{{ $m->pemilikBaru->nama }}</td>
                                <td>
                                    @if ($m->jenis_mutasi === 'pendaftaran')
                                        <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2);">Pendaftaran</span>
                                    @elseif ($m->jenis_mutasi === 'jual_beli')
                                        <span class="badge badge-success">Jual Beli</span>
                                    @elseif ($m->jenis_mutasi === 'waris')
                                        <span class="badge badge-accent">Waris</span>
                                    @elseif ($m->jenis_mutasi === 'hibah')
                                        <span class="badge badge-warning">Hibah</span>
                                    @elseif ($m->jenis_mutasi === 'tukar_guling')
                                        <span class="badge badge-danger">Tukar Guling</span>
                                    @else
                                        <span class="badge">{{ $m->jenis_mutasi }}</span>
                                    @endif
                                </td>
                                <td>{{ $m->tanggal_mutasi->translatedFormat('d M Y') }}</td>
                                <td style="max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                    {{ $m->keterangan ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                    Tidak ada data mutasi yang memenuhi filter dan tanggal terpilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Style for tabs and print views -->
    <style>
        .tab-btn {
            background: transparent;
            border: none;
            padding: 12px 20px;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }
        .tab-btn:hover {
            color: white;
        }
        .tab-btn.active {
            color: #60a5fa;
            border-bottom-color: #60a5fa;
        }
        .font-normal {
            font-weight: 400 !important;
        }
    </style>

    <!-- JS script to handle tab switching and responsive visibility -->
    <script>
        function switchTab(tabName) {
            // Update hidden tab input
            document.getElementById('tab-state').value = tabName;

            // Toggle active buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            const activeBtn = document.querySelector(`.tab-btn[data-target="tab-${tabName}"]`);
            if (activeBtn) activeBtn.classList.add('active');

            // Toggle tab content display
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            const activeContent = document.getElementById(`tab-${tabName}`);
            if (activeContent) activeContent.style.display = 'block';

            // Show/Hide filter inputs based on active tab
            const statusGroup = document.getElementById('filter-status-group');
            const dateGroups = document.querySelectorAll('.date-filter-group');

            if (tabName === 'mutasi') {
                if (statusGroup) statusGroup.style.display = 'none';
                dateGroups.forEach(el => el.style.display = 'block');
            } else if (tabName === 'rekap') {
                // For rekap, filters are not used
                if (statusGroup) statusGroup.style.display = 'none';
                dateGroups.forEach(el => el.style.display = 'none');
            } else {
                // Default: tanah tab
                if (statusGroup) statusGroup.style.display = 'block';
                dateGroups.forEach(el => el.style.display = 'none');
            }
        }

        // Initialize active tab on load
        document.addEventListener('DOMContentLoaded', function() {
            const currentTab = document.getElementById('tab-state').value;
            switchTab(currentTab);
        });
    </script>
@endsection
