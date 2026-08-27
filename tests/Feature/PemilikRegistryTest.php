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

class PemilikRegistryTest extends TestCase
{
    use RefreshDatabase;

    private $jenisHak;

    private $wilayah;

    private $statusTanah;

    protected function setUp(): void
    {
        parent::setUp();

        // Prepare lookup data
        $this->jenisHak = JenisHak::create(['kode' => 'SHM', 'nama' => 'Sertifikat Hak Milik']);
        $this->wilayah = Wilayah::create(['nama_kecamatan' => 'Genteng', 'nama_desa' => 'Genteng']);
        $this->statusTanah = StatusTanah::create(['nama' => 'Aktif']);
    }

    /**
     * Unauthenticated user is redirected to login page.
     */
    public function test_guest_cannot_access_pemilik_registry(): void
    {
        $this->get(route('pemilik.index'))->assertRedirect(route('login'));
        $this->get(route('pemilik.create'))->assertRedirect(route('login'));
        $this->get(route('pemilik.show', 1))->assertRedirect(route('login'));
    }

    /**
     * Admin can view owner index.
     */
    public function test_admin_can_view_pemilik_index(): void
    {
        Pemilik::create([
            'nama' => 'Budi Santoso',
            'nik' => '3171012345670001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1980-05-12',
            'alamat' => 'Jl. Kebon Jeruk No. 15',
            'no_hp' => '081234567890',
        ]);

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('pemilik.index'));

        $response->assertStatus(200);
        $response->assertViewHas('data');
        $response->assertSee('Budi Santoso');
    }

    /**
     * Admin can store a new owner record with a KTP photo upload.
     */
    public function test_admin_can_store_pemilik_with_ktp_upload(): void
    {
        Storage::fake('public');
        $ktpFile = UploadedFile::fake()->image('ktp.jpg');

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->post(route('pemilik.store'), [
                'nama' => 'Andi Wijaya',
                'nik' => '3171012345670002',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1985-08-20',
                'alamat' => 'Jl. Dharmahusada No. 45',
                'no_hp' => '082134567891',
                'email' => 'andi@email.com',
                'foto_ktp' => $ktpFile,
            ]);

        $response->assertRedirect(route('pemilik.index'));
        $response->assertSessionHas('success');

        // Check DB entries
        $this->assertDatabaseHas('pemilik', [
            'nama' => 'Andi Wijaya',
            'nik' => '3171012345670002',
            'email' => 'andi@email.com',
        ]);

        $pemilik = Pemilik::where('nik', '3171012345670002')->first();
        $this->assertNotNull($pemilik->foto_ktp);

        // Verify Storage file
        Storage::disk('public')->assertExists($pemilik->foto_ktp);
    }

    /**
     * Admin can update owner profile.
     */
    public function test_admin_can_update_pemilik(): void
    {
        $pemilik = Pemilik::create([
            'nama' => 'Siti Aminah',
            'nik' => '3171012345670003',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-11-05',
            'alamat' => 'Jl. Dago No. 102',
            'no_hp' => '083134567892',
        ]);

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->put(route('pemilik.update', $pemilik->id), [
                'nama' => 'Siti Aminah Edited',
                'nik' => '3171012345670003', // Keep same NIK to verify unique ignores self
                'tempat_lahir' => 'Bandung Raya',
                'tanggal_lahir' => '1990-11-05',
                'alamat' => 'Jl. Dago No. 105',
                'no_hp' => '083134567899',
                'email' => 'siti@email.com',
            ]);

        $response->assertRedirect(route('pemilik.index'));

        $this->assertDatabaseHas('pemilik', [
            'id' => $pemilik->id,
            'nama' => 'Siti Aminah Edited',
            'tempat_lahir' => 'Bandung Raya',
            'no_hp' => '083134567899',
            'email' => 'siti@email.com',
        ]);
    }

    /**
     * Admin can view owner profile with lands and timeline details.
     */
    public function test_admin_can_view_pemilik_details(): void
    {
        $pemilik = Pemilik::create([
            'nama' => 'Joko Widodo',
            'nik' => '3171012345670004',
            'tempat_lahir' => 'Solo',
            'tanggal_lahir' => '1961-06-21',
            'alamat' => 'Solo',
            'no_hp' => '084134567890',
        ]);

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('pemilik.show', $pemilik->id));

        $response->assertStatus(200);
        $response->assertViewHasAll(['pemilik', 'tanahAktif', 'riwayatTanah']);
    }

    /**
     * Admin cannot delete owner if they still own active land plots.
     */
    public function test_admin_cannot_delete_pemilik_with_active_lands(): void
    {
        $pemilik = Pemilik::create([
            'nama' => 'Joko Widodo',
            'nik' => '3171012345670004',
            'tempat_lahir' => 'Solo',
            'tanggal_lahir' => '1961-06-21',
            'alamat' => 'Solo',
            'no_hp' => '084134567890',
        ]);

        // Create land plot owned by Joko Widodo
        Tanah::create([
            'no_sertifikat' => 'SERT/ACTIVE/111',
            'jenis_hak_id' => $this->jenisHak->id,
            'luas' => 500,
            'alamat' => 'Jl. Istana',
            'wilayah_id' => $this->wilayah->id,
            'status_tanah_id' => $this->statusTanah->id,
            'pemilik_id' => $pemilik->id,
        ]);

        // Attempt deletion
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->delete(route('pemilik.destroy', $pemilik->id));

        // Redirect back with validation/integrity errors
        $response->assertRedirect();
        $response->assertSessionHasErrors(['error']);

        // Joko Widodo should still exist in database
        $this->assertDatabaseHas('pemilik', [
            'id' => $pemilik->id,
        ]);
    }

    /**
     * Admin can delete owner who doesn't own any active lands.
     */
    public function test_admin_can_delete_pemilik_without_active_lands(): void
    {
        Storage::fake('public');
        $ktpFile = UploadedFile::fake()->image('ktp.jpg');
        $path = $ktpFile->store('ktp', 'public');

        $pemilik = Pemilik::create([
            'nama' => 'Joko Widodo',
            'nik' => '3171012345670004',
            'tempat_lahir' => 'Solo',
            'tanggal_lahir' => '1961-06-21',
            'alamat' => 'Solo',
            'no_hp' => '084134567890',
            'foto_ktp' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        // Delete Joko Widodo
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->delete(route('pemilik.destroy', $pemilik->id));

        $response->assertRedirect(route('pemilik.index'));
        $response->assertSessionHas('success');

        // Missing from DB
        $this->assertDatabaseMissing('pemilik', [
            'id' => $pemilik->id,
        ]);

        // KTP image is deleted from storage disk
        Storage::disk('public')->assertMissing($path);
    }
}
