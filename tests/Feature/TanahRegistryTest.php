<?php

namespace Tests\Feature;

use App\Models\JenisHak;
use App\Models\Pemilik;
use App\Models\StatusTanah;
use App\Models\Tanah;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TanahRegistryTest extends TestCase
{
    use RefreshDatabase;

    private $jenisHak;

    private $wilayah;

    private $statusTanah;

    private $pemilik1;

    private $pemilik2;

    protected function setUp(): void
    {
        parent::setUp();

        // Prepare lookup data
        $this->jenisHak = JenisHak::create(['kode' => 'SHM', 'nama' => 'Sertifikat Hak Milik']);
        $this->wilayah = Wilayah::create(['nama_kecamatan' => 'Tegalsari', 'nama_desa' => 'Kedungdoro']);
        $this->statusTanah = StatusTanah::create(['nama' => 'Aktif']);

        // Prepare owners
        $this->pemilik1 = Pemilik::create([
            'nama' => 'Budi Santoso',
            'nik' => '3171012345670001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1980-05-12',
            'alamat' => 'Jl. Kebon Jeruk No. 15',
            'no_hp' => '081234567890',
        ]);

        $this->pemilik2 = Pemilik::create([
            'nama' => 'Andi Wijaya',
            'nik' => '3171012345670002',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1985-08-20',
            'alamat' => 'Jl. Dharmahusada No. 45',
            'no_hp' => '082134567891',
        ]);
    }

    /**
     * Guest cannot access land registry.
     */
    public function test_unauthenticated_user_cannot_access_tanah_registry(): void
    {
        $this->get(route('tanah.index'))->assertRedirect(route('login'));
        $this->get(route('tanah.create'))->assertRedirect(route('login'));
        $this->get(route('tanah.show', 1))->assertRedirect(route('login'));
    }

    /**
     * Admin can view land registry list.
     */
    public function test_admin_can_view_tanah_index(): void
    {
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('tanah.index'));

        $response->assertStatus(200);
        $response->assertViewHas('data');
    }

    /**
     * Admin can view create land form.
     */
    public function test_admin_can_view_create_tanah_form(): void
    {
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('tanah.create'));

        $response->assertStatus(200);
        $response->assertViewHasAll(['jenisHakList', 'wilayahList', 'statusTanahList', 'pemilikList']);
    }

    /**
     * Admin can store a new land parcel with uploads and log initial history.
     */
    public function test_admin_can_store_new_tanah_with_uploads(): void
    {
        Storage::fake('public');

        $certFile = UploadedFile::fake()->create('sertifikat.pdf', 500);
        $photoFile = UploadedFile::fake()->image('lokasi.jpg');

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->post(route('tanah.store'), [
                'no_sertifikat' => 'SERT/12345/KD',
                'jenis_hak_id' => $this->jenisHak->id,
                'luas' => 150.5,
                'alamat' => 'Jl. Mawar No. 10',
                'wilayah_id' => $this->wilayah->id,
                'status_tanah_id' => $this->statusTanah->id,
                'pemilik_id' => $this->pemilik1->id,
                'latitude' => -7.2654,
                'longitude' => 112.7431,
                'dokumen_sertifikat' => $certFile,
                'foto_lokasi' => $photoFile,
            ]);

        $response->assertRedirect(route('tanah.index'));
        $response->assertSessionHas('success');

        // Verify database entry
        $this->assertDatabaseHas('tanah', [
            'no_sertifikat' => 'SERT/12345/KD',
            'jenis_hak_id' => $this->jenisHak->id,
            'luas' => 150.5,
            'alamat' => 'Jl. Mawar No. 10',
            'wilayah_id' => $this->wilayah->id,
            'status_tanah_id' => $this->statusTanah->id,
            'pemilik_id' => $this->pemilik1->id,
            'latitude' => -7.26540000,
            'longitude' => 112.74310000,
        ]);

        $tanah = Tanah::where('no_sertifikat', 'SERT/12345/KD')->first();

        // Verify Documents
        $this->assertDatabaseHas('dokumen_tanah', [
            'tanah_id' => $tanah->id,
            'nama_dokumen' => 'Scan Sertifikat',
        ]);
        $this->assertDatabaseHas('dokumen_tanah', [
            'tanah_id' => $tanah->id,
            'nama_dokumen' => 'Foto Lokasi',
        ]);

        // Verify Storage files
        $docs = $tanah->dokumenTanah;
        foreach ($docs as $doc) {
            Storage::disk('public')->assertExists($doc->file_path);
        }

        // Verify Initial History log
        $this->assertDatabaseHas('riwayat_kepemilikan', [
            'tanah_id' => $tanah->id,
            'pemilik_lama_id' => null,
            'pemilik_baru_id' => $this->pemilik1->id,
            'jenis_mutasi' => 'pendaftaran',
        ]);
    }

    /**
     * Admin can view details and timeline.
     */
    public function test_admin_can_view_tanah_details(): void
    {
        $tanah = Tanah::create([
            'no_sertifikat' => 'SERT/DETAIL/01',
            'jenis_hak_id' => $this->jenisHak->id,
            'luas' => 200,
            'alamat' => 'Jl. Melati',
            'wilayah_id' => $this->wilayah->id,
            'status_tanah_id' => $this->statusTanah->id,
            'pemilik_id' => $this->pemilik1->id,
        ]);

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('tanah.show', $tanah->id));

        $response->assertStatus(200);
        $response->assertViewHas('tanah');
    }

    /**
     * Admin can update land details and trigger mutation log on owner change.
     */
    public function test_admin_can_update_tanah_and_log_mutation(): void
    {
        $tanah = Tanah::create([
            'no_sertifikat' => 'SERT/UPDATE/02',
            'jenis_hak_id' => $this->jenisHak->id,
            'luas' => 200,
            'alamat' => 'Jl. Melati',
            'wilayah_id' => $this->wilayah->id,
            'status_tanah_id' => $this->statusTanah->id,
            'pemilik_id' => $this->pemilik1->id,
        ]);

        // Verify initial state
        $this->assertEquals($this->pemilik1->id, $tanah->pemilik_id);

        // Edit/update owner to pemilik2
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->put(route('tanah.update', $tanah->id), [
                'no_sertifikat' => 'SERT/UPDATE/02-EDITED',
                'jenis_hak_id' => $this->jenisHak->id,
                'luas' => 250,
                'alamat' => 'Jl. Melati Baru',
                'wilayah_id' => $this->wilayah->id,
                'status_tanah_id' => $this->statusTanah->id,
                'pemilik_id' => $this->pemilik2->id, // Changed owner!
            ]);

        $response->assertRedirect(route('tanah.index'));

        // Verify updated database entry
        $this->assertDatabaseHas('tanah', [
            'id' => $tanah->id,
            'no_sertifikat' => 'SERT/UPDATE/02-EDITED',
            'luas' => 250,
            'pemilik_id' => $this->pemilik2->id,
        ]);

        // Verify mutation log generated
        $this->assertDatabaseHas('riwayat_kepemilikan', [
            'tanah_id' => $tanah->id,
            'pemilik_lama_id' => $this->pemilik1->id,
            'pemilik_baru_id' => $this->pemilik2->id,
            'jenis_mutasi' => 'jual_beli',
        ]);
    }

    /**
     * Admin can delete land parcel.
     */
    public function test_admin_can_delete_tanah(): void
    {
        $tanah = Tanah::create([
            'no_sertifikat' => 'SERT/DELETE/03',
            'jenis_hak_id' => $this->jenisHak->id,
            'luas' => 300,
            'alamat' => 'Jl. Dahlia',
            'wilayah_id' => $this->wilayah->id,
            'status_tanah_id' => $this->statusTanah->id,
            'pemilik_id' => $this->pemilik1->id,
        ]);

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->delete(route('tanah.destroy', $tanah->id));

        $response->assertRedirect(route('tanah.index'));
        $this->assertDatabaseMissing('tanah', [
            'id' => $tanah->id,
        ]);
    }
}
