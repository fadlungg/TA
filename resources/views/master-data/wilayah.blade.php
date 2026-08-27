@extends('layouts.app')

@section('title', 'Master Data Wilayah')

@section('content')
    <div class="page-header">
        <h2 class="page-title">Master Data</h2>
        <p class="page-subtitle">Kelola data referensi dusun, RW, dan RT untuk Desa Tunggorono.</p>
    </div>

    <!-- Tab Navigation -->
    <nav class="tab-nav">
        <a href="{{ route('master-data.jenis-hak.index') }}" class="tab-link">Jenis Hak Tanah</a>
        <a href="{{ route('master-data.wilayah.index') }}" class="tab-link active">Wilayah (Dusun/RW/RT)</a>
        <a href="{{ route('master-data.status-tanah.index') }}" class="tab-link">Status Tanah</a>
    </nav>

    <!-- Error Alert if Validation Fails -->
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

    @if (session('error'))
        <div class="alert alert-error">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Data Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Wilayah Administrasi Desa</h3>
            <button class="btn btn-primary" onclick="openAddModal()">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Wilayah RT/RW
            </button>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">No</th>
                        <th>Dusun / Kebayan</th>
                        <th>Rukun Warga (RW)</th>
                        <th>Rukun Tetangga (RT)</th>
                        <th>Desa</th>
                        <th>Kecamatan</th>
                        <th style="width: 200px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: 600; color: white;">{{ $item->nama_dusun ?: '-' }}</td>
                            <td>{{ $item->no_rw ? 'RW ' . $item->no_rw : '-' }}</td>
                            <td>{{ $item->no_rt ? 'RT ' . $item->no_rt : '-' }}</td>
                            <td>{{ $item->nama_desa }}</td>
                            <td>{{ $item->nama_kecamatan }}</td>
                            <td>
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button class="btn btn-secondary btn-sm" onclick="openEditModal({{ $item->id }}, '{{ $item->nama_dusun }}', '{{ $item->no_rw }}', '{{ $item->no_rt }}', '{{ $item->nama_desa }}', '{{ $item->nama_kecamatan }}')">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Ubah
                                    </button>

                                    <form action="{{ route('master-data.wilayah.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus wilayah Dusun {{ $item->nama_dusun }} (RT {{ $item->no_rt }}/RW {{ $item->no_rw }})?')">
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
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 32px;">
                                Belum ada data wilayah desa. Silakan tambahkan data baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Wilayah RT/RW</h3>
                <button class="modal-close" onclick="closeAddModal()">&times;</button>
            </div>
            <form action="{{ route('master-data.wilayah.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_dusun" class="form-label">Nama Dusun / Kebayan</label>
                        <input type="text" id="nama_dusun" name="nama_dusun" class="form-input" placeholder="Contoh: Dusun Krajan" required autocomplete="off">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="no_rw" class="form-label">Nomor RW</label>
                            <input type="text" id="no_rw" name="no_rw" class="form-input" placeholder="Contoh: 02" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="no_rt" class="form-label">Nomor RT</label>
                            <input type="text" id="no_rt" name="no_rt" class="form-input" placeholder="Contoh: 04" autocomplete="off">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="nama_desa" class="form-label">Nama Desa</label>
                            <input type="text" id="nama_desa" name="nama_desa" class="form-input" value="Tunggorono" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="nama_kecamatan" class="form-label">Kecamatan</label>
                            <input type="text" id="nama_kecamatan" name="nama_kecamatan" class="form-input" value="Kutoarjo" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Ubah Wilayah RT/RW</h3>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="" method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_dusun" class="form-label">Nama Dusun / Kebayan</label>
                        <input type="text" id="edit_nama_dusun" name="nama_dusun" class="form-input" required autocomplete="off">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="edit_no_rw" class="form-label">Nomor RW</label>
                            <input type="text" id="edit_no_rw" name="no_rw" class="form-input" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="edit_no_rt" class="form-label">Nomor RT</label>
                            <input type="text" id="edit_no_rt" name="no_rt" class="form-input" autocomplete="off">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="edit_nama_desa" class="form-label">Nama Desa</label>
                            <input type="text" id="edit_nama_desa" name="nama_desa" class="form-input" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="edit_nama_kecamatan" class="form-label">Kecamatan</label>
                            <input type="text" id="edit_nama_kecamatan" name="nama_kecamatan" class="form-input" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
            document.getElementById('nama_dusun').focus();
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
        }

        function openEditModal(id, dusun, rw, rt, desa, kecamatan) {
            const form = document.getElementById('editForm');
            form.action = `{{ url('dashboard/master-data/wilayah') }}/${id}`;
            document.getElementById('edit_nama_dusun').value = dusun;
            document.getElementById('edit_no_rw').value = rw;
            document.getElementById('edit_no_rt').value = rt;
            document.getElementById('edit_nama_desa').value = desa;
            document.getElementById('edit_nama_kecamatan').value = kecamatan;
            document.getElementById('editModal').classList.add('active');
            document.getElementById('edit_nama_dusun').focus();
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            if (event.target == addModal) {
                closeAddModal();
            }
            if (event.target == editModal) {
                closeEditModal();
            }
        }
    </script>
@endsection
