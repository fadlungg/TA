@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h2 class="page-title">Dashboard</h2>
        <p class="page-subtitle">Ringkasan data dan aktivitas pencatatan pertanahan Sipektatu.</p>
    </div>

    <!-- Stats Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 24px;">
        <!-- Card 1: Total Bidang -->
        <div class="card" style="padding: 24px; display: flex; align-items: center; gap: 20px; background: linear-gradient(135deg, rgba(96, 165, 250, 0.12), rgba(96, 165, 250, 0.03)); border: 1px solid rgba(96, 165, 250, 0.2);">
            <div style="padding: 14px; background: rgba(96, 165, 250, 0.15); border-radius: 14px; color: #60a5fa; display: flex; align-items: center; justify-content: center;">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
            </div>
            <div>
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Total Bidang Tanah</div>
                <div style="font-size: 32px; font-weight: 700; color: white; margin-top: 4px;">{{ number_format($totalTanah, 0, ',', '.') }}</div>
                <div style="font-size: 11px; color: #60a5fa; margin-top: 4px; font-weight: 500;">Kapling Terdaftar</div>
            </div>
        </div>

        <!-- Card 2: Total Pemilik -->
        <div class="card" style="padding: 24px; display: flex; align-items: center; gap: 20px; background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(16, 185, 129, 0.03)); border: 1px solid rgba(16, 185, 129, 0.2);">
            <div style="padding: 14px; background: rgba(16, 185, 129, 0.15); border-radius: 14px; color: #10b981; display: flex; align-items: center; justify-content: center;">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Total Pemilik</div>
                <div style="font-size: 32px; font-weight: 700; color: white; margin-top: 4px;">{{ number_format($totalPemilik, 0, ',', '.') }}</div>
                <div style="font-size: 11px; color: #10b981; margin-top: 4px; font-weight: 500;">Profil Anggota Terdaftar</div>
            </div>
        </div>

        <!-- Card 3: Total Luas -->
        <div class="card" style="padding: 24px; display: flex; align-items: center; gap: 20px; background: linear-gradient(135deg, rgba(139, 92, 246, 0.12), rgba(139, 92, 246, 0.03)); border: 1px solid rgba(139, 92, 246, 0.2);">
            <div style="padding: 14px; background: rgba(139, 92, 246, 0.15); border-radius: 14px; color: #8b5cf6; display: flex; align-items: center; justify-content: center;">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
            </div>
            <div>
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Total Luas Lahan</div>
                <div style="font-size: 24px; font-weight: 700; color: white; margin-top: 4px; line-height: 1.2;">
                    {{ number_format($totalLuas, 0, ',', '.') }} m²
                </div>
                <div style="font-size: 11px; color: #8b5cf6; margin-top: 4px; font-weight: 500;">
                    Setara {{ number_format($totalLuas / 10000, 2, ',', '.') }} Hektar (Ha)
                </div>
            </div>
        </div>

        <!-- Card 4: Jenis Hak (Summary) -->
        <div class="card" style="padding: 24px; background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border); display: flex; flex-direction: column; justify-content: center;">
            <div style="font-size: 12px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Breakdown Jenis Hak</div>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @foreach ($jenisHakDistribution->take(4) as $jh)
                    <div style="background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border); font-size: 11px; display: flex; align-items: center; gap: 6px;">
                        <span style="font-weight: 700; color: white;">{{ $jh->kode }}</span>
                        <span style="color: var(--text-muted);">({{ $jh->jumlah_tanah }})</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 20px; margin-bottom: 24px;">
        <!-- Chart 1: Distribusi Wilayah -->
        <div class="card" style="padding: 20px;">
            <div class="card-header" style="margin-bottom: 20px;">
                <h3 class="card-title">Sebaran Bidang Tanah per Wilayah Desa</h3>
            </div>
            <div style="height: 280px; position: relative;">
                <canvas id="wilayahChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Proporsi Jenis Hak -->
        <div class="card" style="padding: 20px;">
            <div class="card-header" style="margin-bottom: 20px;">
                <h3 class="card-title">Proporsi Jenis Hak Lahan</h3>
            </div>
            <div style="height: 280px; position: relative; display: flex; justify-content: center;">
                <canvas id="jenisHakChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities Table -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title">Aktivitas & Mutasi Terakhir</h3>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 140px;">Waktu</th>
                        <th>Aktivitas</th>
                        <th>Sertifikat Tanah</th>
                        <th>Pemilik Baru</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentActivities as $activity)
                        <tr>
                            <td style="color: var(--text-muted); font-size: 12px;">
                                {{ $activity->created_at->diffForHumans() }}
                            </td>
                            <td>
                                @if ($activity->jenis_mutasi === 'pendaftaran')
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2);">Pendaftaran</span>
                                        <span>Registrasi Lahan Baru</span>
                                    </div>
                                @else
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        @if ($activity->jenis_mutasi === 'jual_beli')
                                            <span class="badge badge-success">Jual Beli</span>
                                        @elseif ($activity->jenis_mutasi === 'waris')
                                            <span class="badge badge-accent">Waris</span>
                                        @elseif ($activity->jenis_mutasi === 'hibah')
                                            <span class="badge badge-warning">Hibah</span>
                                        @elseif ($activity->jenis_mutasi === 'tukar_guling')
                                            <span class="badge badge-danger">Tukar Guling</span>
                                        @else
                                            <span class="badge">{{ $activity->jenis_mutasi }}</span>
                                        @endif
                                        <span>Peralihan Hak</span>
                                    </div>
                                @endif
                            </td>
                            <td style="font-weight: 600; color: white;">
                                <a href="{{ route('tanah.show', $activity->tanah->id) }}" style="color: #60a5fa; text-decoration: none;">
                                    {{ $activity->tanah->no_sertifikat }}
                                </a>
                            </td>
                            <td style="font-weight: 500; color: white;">
                                <a href="{{ route('pemilik.show', $activity->pemilikBaru->id) }}" style="color: white; text-decoration: none;">
                                    {{ $activity->pemilikBaru->nama }}
                                </a>
                            </td>
                            <td style="color: var(--text-muted); font-size: 12px; max-width: 300px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                {{ $activity->keterangan ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 32px;">
                                Belum ada aktivitas atau mutasi kepemilikan tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ChartJS Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js Default Font Styling
            Chart.defaults.color = 'rgba(255, 255, 255, 0.5)';
            Chart.defaults.font.family = "'Outfit', sans-serif";

            // 1. Wilayah Distribution Chart
            const ctxWilayah = document.getElementById('wilayahChart').getContext('2d');
            new Chart(ctxWilayah, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($wilayahDistribution as $wd)
                            '{{ $wd->nama_desa }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Jumlah Bidang Tanah',
                        data: [
                            @foreach($wilayahDistribution as $wd)
                                {{ $wd->jumlah_tanah }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(96, 165, 250, 0.65)',
                        borderColor: '#60a5fa',
                        borderWidth: 1.5,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // 2. Jenis Hak Proporsion Chart
            const ctxHak = document.getElementById('jenisHakChart').getContext('2d');
            new Chart(ctxHak, {
                type: 'doughnut',
                data: {
                    labels: [
                        @foreach($jenisHakDistribution as $jh)
                            '{{ $jh->kode }}',
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach($jenisHakDistribution as $jh)
                                {{ $jh->jumlah_tanah }},
                            @endforeach
                        ],
                        backgroundColor: [
                            'rgba(96, 165, 250, 0.75)',   // Blue (SHM)
                            'rgba(16, 185, 129, 0.75)',   // Emerald (HGB)
                            'rgba(139, 92, 246, 0.75)',   // Purple (HGU)
                            'rgba(245, 158, 11, 0.75)',   // Orange (Girik)
                            'rgba(239, 68, 68, 0.75)'     // Red (Hak Pakai)
                        ],
                        borderWidth: 2,
                        borderColor: '#111327'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 16,
                                boxWidth: 12
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        });
    </script>
@endsection
