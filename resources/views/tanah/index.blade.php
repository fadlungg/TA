@extends('layouts.app')

@section('title', 'Data Tanah')

@section('content')
    <div class="page-header">
        <h2 class="page-title">Data Tanah</h2>
        <p class="page-subtitle">Kelola dan telusuri seluruh data pendaftaran kapling tanah Sipektatu.</p>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="alert alert-success">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Data Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Registrasi Kapling Tanah</h3>
            <a href="{{ route('tanah.create') }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Registrasi Tanah Baru
            </a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>No. Sertifikat / Persil</th>
                        <th>Luas</th>
                        <th>Lokasi (Desa/Kecamatan)</th>
                        <th>Jenis Hak</th>
                        <th>Status</th>
                        <th>Pemilik Saat Ini</th>
                        <th style="width: 250px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                             <td style="font-weight: 600; color: white;">
                                 {{ $item->no_sertifikat }}
                                 @if($item->no_letter_c || $item->no_persil)
                                     <div style="font-size: 11px; color: var(--text-muted); font-weight: 400; margin-top: 2px;">
                                         C: {{ $item->no_letter_c ?: '-' }} / Persil: {{ $item->no_persil ?: '-' }}
                                     </div>
                                 @endif
                             </td>
                             <td>{{ number_format($item->luas, 0, ',', '.') }} m²</td>
                             <td>
                                 <div>{{ $item->wilayah->nama_dusun ?: 'Desa '.$item->wilayah->nama_desa }}</div>
                                 <div style="font-size: 11px; color: var(--text-muted);">
                                     @if($item->wilayah->no_rt || $item->wilayah->no_rw)
                                         RT {{ $item->wilayah->no_rt ?: '00' }} / RW {{ $item->wilayah->no_rw ?: '00' }}
                                     @else
                                         Kec. {{ $item->wilayah->nama_kecamatan }}
                                     @endif
                                 </div>
                             </td>
                            <td>
                                <span class="badge badge-accent">{{ $item->jenisHak->kode }}</span>
                            </td>
                            <td>
                                @if($item->statusTanah->nama == 'Aktif')
                                    <span class="badge badge-success">{{ $item->statusTanah->nama }}</span>
                                @elseif($item->statusTanah->nama == 'Sengketa')
                                    <span class="badge badge-danger">{{ $item->statusTanah->nama }}</span>
                                @elseif($item->statusTanah->nama == 'Dijual')
                                    <span class="badge badge-accent">{{ $item->statusTanah->nama }}</span>
                                @else
                                    <span class="badge" style="background: rgba(255, 255, 255, 0.08); color: var(--text-muted); border: 1px solid var(--border);">{{ $item->statusTanah->nama }}</span>
                                @endif
                            </td>
                            <td style="font-weight: 500;">{{ $item->pemilik->nama }}</td>
                            <td>
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('tanah.show', $item->id) }}" class="btn btn-secondary btn-sm" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa; border-color: rgba(59, 130, 246, 0.2);">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 01-6 0m6 0a6 6 0 0112 0v3a3 3 0 01-3 3H6a3 3 0 01-3-3v-3z" />
                                        </svg>
                                        Detail
                                    </a>

                                    <a href="{{ route('tanah.edit', $item->id) }}" class="btn btn-secondary btn-sm">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Ubah
                                    </a>

                                    <form action="{{ route('tanah.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tanah dengan sertifikat {{ $item->no_sertifikat }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 48px;">
                                <div style="margin-bottom: 8px;">
                                    <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                </div>
                                Belum ada data kapling tanah terdaftar. Silakan lakukan registrasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
