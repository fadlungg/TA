<?php

namespace Tests\Unit;

use App\Models\ActivityLog;
use App\Models\DokumenTanah;
use App\Models\JenisHak;
use App\Models\Pemilik;
use App\Models\RiwayatKepemilikan;
use App\Models\StatusTanah;
use App\Models\Tanah;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test database relationships and model integration.
     */
    public function test_database_relationships_and_integration(): void
    {
        // 1. Create User
        $user = User::factory()->create();

        // 2. Create Wilayah
        $wilayah = Wilayah::create([
            'nama_kecamatan' => 'Kecamatan A',
            'nama_desa' => 'Desa B',
        ]);

        // 3. Create JenisHak
        $jenisHak = JenisHak::create([
            'kode' => 'SHM',
            'nama' => 'Sertifikat Hak Milik',
        ]);

        // 4. Create StatusTanah
        $statusTanah = StatusTanah::create([
            'nama' => 'Aktif',
        ]);

        // 5. Create Pemilik (Old and New)
        $pemilikLama = Pemilik::create([
            'nama' => 'Budi',
            'nik' => '1234567890123456',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1980-01-01',
            'alamat' => 'Jl. Mawar No. 1',
            'no_hp' => '08123456789',
        ]);

        $pemilikBaru = Pemilik::create([
            'nama' => 'Andi',
            'nik' => '9876543210987654',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-05-05',
            'alamat' => 'Jl. Melati No. 2',
            'no_hp' => '08987654321',
        ]);

        // 6. Create Tanah
        $tanah = Tanah::create([
            'no_sertifikat' => '12.34.56.78.1.00001',
            'jenis_hak_id' => $jenisHak->id,
            'luas' => 500.5,
            'alamat' => 'Jl. Raya No. 12',
            'wilayah_id' => $wilayah->id,
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status_tanah_id' => $statusTanah->id,
            'pemilik_id' => $pemilikBaru->id,
        ]);

        // 7. Create DokumenTanah
        $dokumen = DokumenTanah::create([
            'tanah_id' => $tanah->id,
            'nama_dokumen' => 'Sertifikat Asli',
            'file_path' => 'dokumen/shm_00001.pdf',
            'uploaded_at' => now(),
        ]);

        // 8. Create RiwayatKepemilikan
        $riwayat = RiwayatKepemilikan::create([
            'tanah_id' => $tanah->id,
            'pemilik_lama_id' => $pemilikLama->id,
            'pemilik_baru_id' => $pemilikBaru->id,
            'jenis_mutasi' => 'jual_beli',
            'tanggal_mutasi' => '2026-08-26',
            'dokumen_path' => 'dokumen/akta_jual_beli_00001.pdf',
            'keterangan' => 'Transaksi jual beli antara Budi dan Andi',
        ]);

        // 9. Create ActivityLog
        $log = ActivityLog::create([
            'user_id' => $user->id,
            'aksi' => 'create_tanah',
            'model' => Tanah::class,
            'model_id' => $tanah->id,
            'keterangan' => 'Menambahkan data tanah baru dengan Sertifikat SHM 00001',
        ]);

        // Assert relations
        $this->assertEquals(1, $wilayah->tanah()->count());
        $this->assertEquals($wilayah->id, $tanah->wilayah->id);

        $this->assertEquals(1, $jenisHak->tanah()->count());
        $this->assertEquals($jenisHak->id, $tanah->jenisHak->id);

        $this->assertEquals(1, $statusTanah->tanah()->count());
        $this->assertEquals($statusTanah->id, $tanah->statusTanah->id);

        $this->assertEquals(1, $pemilikBaru->tanah()->count());
        $this->assertEquals($pemilikBaru->id, $tanah->pemilik->id);

        $this->assertEquals(1, $tanah->dokumenTanah()->count());
        $this->assertEquals($tanah->id, $dokumen->tanah->id);

        $this->assertEquals(1, $tanah->riwayatKepemilikan()->count());
        $this->assertEquals($tanah->id, $riwayat->tanah->id);
        $this->assertEquals($pemilikLama->id, $riwayat->pemilikLama->id);
        $this->assertEquals($pemilikBaru->id, $riwayat->pemilikBaru->id);

        $this->assertEquals($user->id, $log->user->id);
    }
}
