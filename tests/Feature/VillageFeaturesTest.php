<?php

namespace Tests\Feature;

use App\Models\JenisHak;
use App\Models\Pemilik;
use App\Models\StatusTanah;
use App\Models\Tanah;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillageFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private $jenisHak;

    private $statusTanah;

    private $pemilik;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jenisHak = JenisHak::create(['kode' => 'SHM', 'nama' => 'Sertifikat Hak Milik']);
        $this->statusTanah = StatusTanah::create(['nama' => 'Aktif']);
        $this->pemilik = Pemilik::create([
            'nama' => 'Sri Utami',
            'nik' => '9876543210987654',
            'tempat_lahir' => 'Purworejo',
            'tanggal_lahir' => '1990-08-15',
            'alamat' => 'Tunggorono',
            'no_hp' => '087654321098',
        ]);
    }

    /**
     * Test admin can create a wilayah with dusun, rw, and rt.
     */
    public function test_admin_can_create_wilayah_with_village_subdivisions(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])
            ->post(route('master-data.wilayah.store'), [
                'nama_dusun' => 'Dusun Karangrejo',
                'no_rw' => '02',
                'no_rt' => '04',
                'nama_desa' => 'Tunggorono',
                'nama_kecamatan' => 'Kutoarjo',
            ]);

        $response->assertRedirect(route('master-data.wilayah.index'));
        $this->assertDatabaseHas('wilayah', [
            'nama_dusun' => 'Dusun Karangrejo',
            'no_rw' => '02',
            'no_rt' => '04',
            'nama_desa' => 'Tunggorono',
            'nama_kecamatan' => 'Kutoarjo',
        ]);
    }

    /**
     * Test admin can register a land plot with Letter C, Persil, Klas, and Bengkok.
     */
    public function test_admin_can_register_tanah_with_letter_c_and_persil(): void
    {
        $wilayah = Wilayah::create([
            'nama_kecamatan' => 'Kutoarjo',
            'nama_desa' => 'Tunggorono',
            'nama_dusun' => 'Krajan',
            'no_rw' => '01',
            'no_rt' => '02',
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->post(route('tanah.store'), [
                'no_sertifikat' => 'SERT/TUNGGORONO/005',
                'no_letter_c' => 'C 145',
                'no_persil' => 'Persil 12a',
                'klas_tanah' => 'S.II',
                'status_bengkok' => 'Bengkok Kepala Desa',
                'jenis_hak_id' => $this->jenisHak->id,
                'luas' => 1200,
                'alamat' => 'Sawah Krajan RT 02/RW 01',
                'wilayah_id' => $wilayah->id,
                'status_tanah_id' => $this->statusTanah->id,
                'pemilik_id' => $this->pemilik->id,
            ]);

        $response->assertRedirect(route('tanah.index'));
        $this->assertDatabaseHas('tanah', [
            'no_sertifikat' => 'SERT/TUNGGORONO/005',
            'no_letter_c' => 'C 145',
            'no_persil' => 'Persil 12a',
            'klas_tanah' => 'S.II',
            'status_bengkok' => 'Bengkok Kepala Desa',
        ]);

        // Check index and details view contains the data
        $tanah = Tanah::where('no_sertifikat', 'SERT/TUNGGORONO/005')->first();
        $responseShow = $this->withSession(['admin_logged_in' => true])
            ->get(route('tanah.show', $tanah->id));

        $responseShow->assertStatus(200);
        $responseShow->assertSee('C 145');
        $responseShow->assertSee('Persil 12a');
        $responseShow->assertSee('S.II');
        $responseShow->assertSee('Bengkok Kepala Desa');
    }
}
