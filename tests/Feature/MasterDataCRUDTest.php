<?php

namespace Tests\Feature;

use App\Models\JenisHak;
use App\Models\Pemilik;
use App\Models\StatusTanah;
use App\Models\Tanah;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataCRUDTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest is redirected to login when accessing Master Data.
     */
    public function test_unauthenticated_user_cannot_access_master_data(): void
    {
        $this->get(route('master-data.jenis-hak.index'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $this->get(route('master-data.wilayah.index'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $this->get(route('master-data.status-tanah.index'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');
    }

    /**
     * Test admin can perform CRUD on Jenis Hak.
     */
    public function test_admin_can_manage_jenis_hak(): void
    {
        // 1. Access Index
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('master-data.jenis-hak.index'));

        $response->assertStatus(200);
        $response->assertViewHas('data');

        // 2. Store Item
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->post(route('master-data.jenis-hak.store'), [
                'kode' => 'SHM_TEST',
                'nama' => 'Sertifikat Hak Milik Test',
            ]);

        $response->assertRedirect(route('master-data.jenis-hak.index'));
        $this->assertDatabaseHas('jenis_hak', [
            'kode' => 'SHM_TEST',
            'nama' => 'Sertifikat Hak Milik Test',
        ]);

        $jenisHak = JenisHak::where('kode', 'SHM_TEST')->first();

        // 3. Update Item
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->put(route('master-data.jenis-hak.update', $jenisHak->id), [
                'kode' => 'SHM_TEST_EDIT',
                'nama' => 'Sertifikat Hak Milik Test Edit',
            ]);

        $response->assertRedirect(route('master-data.jenis-hak.index'));
        $this->assertDatabaseHas('jenis_hak', [
            'id' => $jenisHak->id,
            'kode' => 'SHM_TEST_EDIT',
            'nama' => 'Sertifikat Hak Milik Test Edit',
        ]);

        // 4. Delete Item
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->delete(route('master-data.jenis-hak.destroy', $jenisHak->id));

        $response->assertRedirect(route('master-data.jenis-hak.index'));
        $this->assertDatabaseMissing('jenis_hak', [
            'id' => $jenisHak->id,
        ]);
    }

    /**
     * Test admin can perform CRUD on Wilayah.
     */
    public function test_admin_can_manage_wilayah(): void
    {
        // 1. Access Index
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('master-data.wilayah.index'));

        $response->assertStatus(200);

        // 2. Store Item
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->post(route('master-data.wilayah.store'), [
                'nama_kecamatan' => 'Kecamatan Test',
                'nama_desa' => 'Desa Test',
            ]);

        $response->assertRedirect(route('master-data.wilayah.index'));
        $this->assertDatabaseHas('wilayah', [
            'nama_kecamatan' => 'Kecamatan Test',
            'nama_desa' => 'Desa Test',
        ]);

        $wilayah = Wilayah::where('nama_desa', 'Desa Test')->first();

        // 3. Update Item
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->put(route('master-data.wilayah.update', $wilayah->id), [
                'nama_kecamatan' => 'Kecamatan Test Edit',
                'nama_desa' => 'Desa Test Edit',
            ]);

        $response->assertRedirect(route('master-data.wilayah.index'));
        $this->assertDatabaseHas('wilayah', [
            'id' => $wilayah->id,
            'nama_kecamatan' => 'Kecamatan Test Edit',
            'nama_desa' => 'Desa Test Edit',
        ]);

        // 4. Delete Item
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->delete(route('master-data.wilayah.destroy', $wilayah->id));

        $response->assertRedirect(route('master-data.wilayah.index'));
        $this->assertDatabaseMissing('wilayah', [
            'id' => $wilayah->id,
        ]);
    }

    /**
     * Test admin can perform CRUD on Status Tanah.
     */
    public function test_admin_can_manage_status_tanah(): void
    {
        // 1. Access Index
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('master-data.status-tanah.index'));

        $response->assertStatus(200);

        // 2. Store Item
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->post(route('master-data.status-tanah.store'), [
                'nama' => 'Status Test',
            ]);

        $response->assertRedirect(route('master-data.status-tanah.index'));
        $this->assertDatabaseHas('status_tanah', [
            'nama' => 'Status Test',
        ]);

        $statusTanah = StatusTanah::where('nama', 'Status Test')->first();

        // 3. Update Item
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->put(route('master-data.status-tanah.update', $statusTanah->id), [
                'nama' => 'Status Test Edit',
            ]);

        $response->assertRedirect(route('master-data.status-tanah.index'));
        $this->assertDatabaseHas('status_tanah', [
            'id' => $statusTanah->id,
            'nama' => 'Status Test Edit',
        ]);

        // 4. Delete Item
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->delete(route('master-data.status-tanah.destroy', $statusTanah->id));

        $response->assertRedirect(route('master-data.status-tanah.index'));
        $this->assertDatabaseMissing('status_tanah', [
            'id' => $statusTanah->id,
        ]);
    }

    /**
     * Test cannot delete Jenis Hak if it has linked tanah.
     */
    public function test_cannot_delete_referenced_jenis_hak(): void
    {
        $jenisHak = JenisHak::create(['kode' => 'REF', 'nama' => 'Referenced']);
        $wilayah = Wilayah::create(['nama_kecamatan' => 'A', 'nama_desa' => 'B']);
        $statusTanah = StatusTanah::create(['nama' => 'C']);
        $pemilik = Pemilik::create([
            'nama' => 'Test Pemilik',
            'nik' => '1234567890123456',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'alamat' => 'Address',
            'no_hp' => '08123456789',
        ]);

        // Create tanah linked to this jenis_hak
        Tanah::create([
            'no_sertifikat' => '12345',
            'jenis_hak_id' => $jenisHak->id,
            'luas' => 100,
            'alamat' => 'Address',
            'wilayah_id' => $wilayah->id,
            'latitude' => 0.0,
            'longitude' => 0.0,
            'status_tanah_id' => $statusTanah->id,
            'pemilik_id' => $pemilik->id,
        ]);

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->delete(route('master-data.jenis-hak.destroy', $jenisHak->id));

        $response->assertRedirect(route('master-data.jenis-hak.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('jenis_hak', ['id' => $jenisHak->id]);
    }

    /**
     * Test cannot delete Wilayah if it has linked tanah.
     */
    public function test_cannot_delete_referenced_wilayah(): void
    {
        $jenisHak = JenisHak::create(['kode' => 'A', 'nama' => 'B']);
        $wilayah = Wilayah::create(['nama_kecamatan' => 'REF_KEC', 'nama_desa' => 'REF_DESA']);
        $statusTanah = StatusTanah::create(['nama' => 'C']);
        $pemilik = Pemilik::create([
            'nama' => 'Test Pemilik',
            'nik' => '1234567890123456',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'alamat' => 'Address',
            'no_hp' => '08123456789',
        ]);

        Tanah::create([
            'no_sertifikat' => '12345',
            'jenis_hak_id' => $jenisHak->id,
            'luas' => 100,
            'alamat' => 'Address',
            'wilayah_id' => $wilayah->id,
            'latitude' => 0.0,
            'longitude' => 0.0,
            'status_tanah_id' => $statusTanah->id,
            'pemilik_id' => $pemilik->id,
        ]);

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->delete(route('master-data.wilayah.destroy', $wilayah->id));

        $response->assertRedirect(route('master-data.wilayah.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('wilayah', ['id' => $wilayah->id]);
    }

    /**
     * Test cannot delete Status Tanah if it has linked tanah.
     */
    public function test_cannot_delete_referenced_status_tanah(): void
    {
        $jenisHak = JenisHak::create(['kode' => 'A', 'nama' => 'B']);
        $wilayah = Wilayah::create(['nama_kecamatan' => 'C', 'nama_desa' => 'D']);
        $statusTanah = StatusTanah::create(['nama' => 'REF_STATUS']);
        $pemilik = Pemilik::create([
            'nama' => 'Test Pemilik',
            'nik' => '1234567890123456',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'alamat' => 'Address',
            'no_hp' => '08123456789',
        ]);

        Tanah::create([
            'no_sertifikat' => '12345',
            'jenis_hak_id' => $jenisHak->id,
            'luas' => 100,
            'alamat' => 'Address',
            'wilayah_id' => $wilayah->id,
            'latitude' => 0.0,
            'longitude' => 0.0,
            'status_tanah_id' => $statusTanah->id,
            'pemilik_id' => $pemilik->id,
        ]);

        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->delete(route('master-data.status-tanah.destroy', $statusTanah->id));

        $response->assertRedirect(route('master-data.status-tanah.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('status_tanah', ['id' => $statusTanah->id]);
    }
}
