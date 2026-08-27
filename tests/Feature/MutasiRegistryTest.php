<?php

namespace Tests\Feature;

use App\Models\JenisHak;
use App\Models\Pemilik;
use App\Models\RiwayatKepemilikan;
use App\Models\StatusTanah;
use App\Models\Tanah;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MutasiRegistryTest extends TestCase
{
    use RefreshDatabase;

    private $jenisHak;

    private $wilayah;

    private $statusTanah;

    private $pemilik1;

    private $pemilik2;

    private $tanah;

    protected function setUp(): void
    {
        parent::setUp();

        // Prepare lookup data
        $this->jenisHak = JenisHak::create(['kode' => 'SHM', 'nama' => 'Sertifikat Hak Milik']);
        $this->wilayah = Wilayah::create(['nama_kecamatan' => 'Genteng', 'nama_desa' => 'Genteng']);
        $this->statusTanah = StatusTanah::create(['nama' => 'Aktif']);

        // Prepare owners
        $this->pemilik1 = Pemilik::create([
            'nama' => 'Budi Santoso',
            'nik' => '1234567890123456',
            'tempat_lahir' => 'Banyuwangi',
            'tanggal_lahir' => '1985-05-12',
            'alamat' => 'Genteng',
            'no_hp' => '081234567890',
        ]);

        $this->pemilik2 = Pemilik::create([
            'nama' => 'Andi Wijaya',
            'nik' => '1234567890123457',
            'tempat_lahir' => 'Banyuwangi',
            'tanggal_lahir' => '1990-11-20',
            'alamat' => 'Rogojampi',
            'no_hp' => '081234567891',
        ]);

        // Create initial Land (automatically creates a 'pendaftaran' riwayat_kepemilikan log)
        $this->tanah = Tanah::create([
            'no_sertifikat' => 'SERT/GENTENG/001',
            'jenis_hak_id' => $this->jenisHak->id,
            'luas' => 500,
            'alamat' => 'Jl. Gajah Mada No. 10',
            'wilayah_id' => $this->wilayah->id,
            'status_tanah_id' => $this->statusTanah->id,
            'pemilik_id' => $this->pemilik1->id,
        ]);

        RiwayatKepemilikan::create([
            'tanah_id' => $this->tanah->id,
            'pemilik_lama_id' => null,
            'pemilik_baru_id' => $this->pemilik1->id,
            'jenis_mutasi' => 'pendaftaran',
            'tanggal_mutasi' => now()->toDateString(),
            'keterangan' => 'Pendaftaran tanah awal.',
        ]);
    }

    /**
     * Guests are redirected to login.
     */
    public function test_guest_cannot_access_mutasi_routes(): void
    {
        $this->get(route('mutasi.index'))->assertRedirect(route('login'));
        $this->get(route('mutasi.create'))->assertRedirect(route('login'));
    }

    /**
     * Admin can view mutation logs.
     */
    public function test_admin_can_view_mutasi_index(): void
    {
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('mutasi.index'));

        $response->assertStatus(200);
        $response->assertViewHas('data');
        $response->assertSee('SERT/GENTENG/001');
    }

    /**
     * Admin can view mutation create form.
     */
    public function test_admin_can_view_mutasi_create_form(): void
    {
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('mutasi.create'));

        $response->assertStatus(200);
        $response->assertViewHasAll(['tanahList', 'pemilikList']);
    }

    /**
     * Admin cannot transfer land ownership to the same active owner.
     */
    public function test_admin_cannot_mutate_to_same_owner(): void
    {
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->post(route('mutasi.store'), [
                'tanah_id' => $this->tanah->id,
                'pemilik_baru_id' => $this->pemilik1->id, // Same as current owner
                'jenis_mutasi' => 'jual_beli',
                'tanggal_mutasi' => now()->toDateString(),
                'keterangan' => 'Mutasi ke diri sendiri (invalid)',
            ]);

        $response->assertSessionHasErrors(['pemilik_baru_id']);
        $this->assertEquals($this->pemilik1->id, $this->tanah->fresh()->pemilik_id);
    }

    /**
     * Admin can successfully record a mutation transaction.
     */
    public function test_admin_can_record_mutation_transaction(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('akta_jual_beli.pdf', 1000);

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->post(route('mutasi.store'), [
                'tanah_id' => $this->tanah->id,
                'pemilik_baru_id' => $this->pemilik2->id, // Transfer to pemilik2
                'jenis_mutasi' => 'waris',
                'tanggal_mutasi' => now()->toDateString(),
                'keterangan' => 'Pewarisan hak tanah.',
                'dokumen_mutasi' => $file,
            ]);

        $response->assertRedirect(route('mutasi.index'));
        $response->assertSessionHas('success');

        // Verify land owner updated
        $this->assertEquals($this->pemilik2->id, $this->tanah->fresh()->pemilik_id);

        // Verify DB contains mutation log
        $this->assertDatabaseHas('riwayat_kepemilikan', [
            'tanah_id' => $this->tanah->id,
            'pemilik_lama_id' => $this->pemilik1->id,
            'pemilik_baru_id' => $this->pemilik2->id,
            'jenis_mutasi' => 'waris',
        ]);

        $log = RiwayatKepemilikan::where('pemilik_baru_id', $this->pemilik2->id)->first();
        $this->assertNotNull($log->dokumen_path);

        // Verify document exists
        Storage::disk('public')->assertExists($log->dokumen_path);
    }

    /**
     * Admin cannot delete an initial registration log.
     */
    public function test_admin_cannot_delete_initial_registration_log(): void
    {
        // Initial registration log
        $log = RiwayatKepemilikan::where('jenis_mutasi', 'pendaftaran')->first();

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->delete(route('mutasi.destroy', $log->id));

        $response->assertSessionHasErrors(['error']);
        $this->assertDatabaseHas('riwayat_kepemilikan', ['id' => $log->id]);
    }

    /**
     * Admin can delete a mutation and revert owner.
     */
    public function test_admin_can_delete_mutation_and_revert_owner(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('akta.pdf', 500);
        $path = $file->store('mutasi', 'public');

        // Log mutation
        $log = RiwayatKepemilikan::create([
            'tanah_id' => $this->tanah->id,
            'pemilik_lama_id' => $this->pemilik1->id,
            'pemilik_baru_id' => $this->pemilik2->id,
            'jenis_mutasi' => 'hibah',
            'tanggal_mutasi' => now()->toDateString(),
            'dokumen_path' => $path,
            'keterangan' => 'Hibah sertifikat.',
        ]);

        // Manually update active land owner to match mutation destination
        $this->tanah->update(['pemilik_id' => $this->pemilik2->id]);
        $this->assertEquals($this->pemilik2->id, $this->tanah->fresh()->pemilik_id);

        Storage::disk('public')->assertExists($path);

        // Delete the mutation
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->delete(route('mutasi.destroy', $log->id));

        $response->assertRedirect(route('mutasi.index'));
        $response->assertSessionHas('success');

        // Assert log missing from DB
        $this->assertDatabaseMissing('riwayat_kepemilikan', ['id' => $log->id]);

        // Assert document file removed
        Storage::disk('public')->assertMissing($path);

        // Assert land owner reverted to pemilik1
        $this->assertEquals($this->pemilik1->id, $this->tanah->fresh()->pemilik_id);
    }
}
