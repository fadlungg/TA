@extends('layouts.app')

@section('title', 'Kepemilikan & Mutasi')

@section('content')
    <div class="page-header">
        <h2 class="page-title">Kepemilikan & Mutasi</h2>
        <p class="page-subtitle">Kelola pencatatan dan log kronologi kepemilikan kapling tanah.</p>
    </div>

    <!-- Notifications -->
    @if (session('success'))
        <div class="alert alert-success">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Perubahan Kepemilikan</h3>
            <a href="{{ route('mutasi.create') }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Catat Mutasi Baru
            </a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Sertifikat Tanah</th>
                        <th>Pemilik Sebelumnya</th>
                        <th>Pemilik Baru (Sekarang)</th>
                        <th>Jenis Mutasi</th>
                        <th>Tanggal Mutasi</th>
                        <th>Dokumen Akta</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: 600; color: white;">
                                <a href="{{ route('tanah.show', $item->tanah->id) }}" style="color: #60a5fa; text-decoration: none; font-weight: 600;">
                                    {{ $item->tanah->no_sertifikat }}
                                </a>
                            </td>
                            <td>
                                @if ($item->pemilikLama)
                                    <a href="{{ route('pemilik.show', $item->pemilikLama->id) }}" style="color: white; text-decoration: none; font-weight: 500;">
                                        {{ $item->pemilikLama->nama }}
                                    </a>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Pendaftaran Awal</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pemilik.show', $item->pemilikBaru->id) }}" style="color: white; text-decoration: none; font-weight: 500;">
                                    {{ $item->pemilikBaru->nama }}
                                </a>
                            </td>
                            <td>
                                @if ($item->jenis_mutasi === 'pendaftaran')
                                    <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2);">Pendaftaran</span>
                                @elseif ($item->jenis_mutasi === 'jual_beli')
                                    <span class="badge badge-success">Jual Beli</span>
                                @elseif ($item->jenis_mutasi === 'waris')
                                    <span class="badge badge-accent">Waris</span>
                                @elseif ($item->jenis_mutasi === 'hibah')
                                    <span class="badge badge-warning">Hibah</span>
                                @elseif ($item->jenis_mutasi === 'tukar_guling')
                                    <span class="badge badge-danger">Tukar Guling</span>
                                @else
                                    <span class="badge">{{ $item->jenis_mutasi }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $item->tanggal_mutasi->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                @if ($item->dokumen_path)
                                    <a href="{{ asset('storage/' . $item->dokumen_path) }}" target="_blank" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; font-size: 11px;">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Unduh Akta
                                    </a>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px; font-style: italic;">Tidak ada berkas</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->jenis_mutasi !== 'pendaftaran' && !is_null($item->pemilik_lama_id))
                                    <form action="{{ route('mutasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan mutasi ini? Tindakan ini akan mengembalikan pemilik tanah saat ini ke pemilik sebelumnya ({{ $item->pemilikLama->nama }}).')" style="text-align: center;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 8px;">
                                            Batal & Hapus
                                        </button>
                                    </form>
                                @else
                                    <div style="text-align: center; color: var(--text-muted); font-size: 11px; font-style: italic;">
                                        Permanent Log
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 48px;">
                                <div style="margin-bottom: 8px;">
                                    <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </div>
                                Belum ada riwayat transaksi mutasi tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
