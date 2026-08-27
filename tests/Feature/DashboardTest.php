<?php

namespace Tests\Feature;

use App\Models\JenisHak;
use App\Models\Pemilik;
use App\Models\RiwayatKepemilikan;
use App\Models\StatusTanah;
use App\Models\Tanah;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private $jenisHak;

    private $wilayah;

    private $statusTanah;

    private $pemilik;

    private $tanah;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic dependencies
        $this->jenisHak = JenisHak::create(['kode' => 'SHM', 'nama' => 'Sertifikat Hak Milik']);
        $this->wilayah = Wilayah::create(['nama_kecamatan' => 'Genteng', 'nama_desa' => 'Genteng']);
        $this->statusTanah = StatusTanah::create(['nama' => 'Aktif']);

        $this->pemilik = Pemilik::create([
            'nama' => 'Budi Santoso',
            'nik' => '1234567890123456',
            'tempat_lahir' => 'Banyuwangi',
            'tanggal_lahir' => '1985-05-12',
            'alamat' => 'Genteng',
            'no_hp' => '081234567890',
        ]);

        $this->tanah = Tanah::create([
            'no_sertifikat' => 'SERT/GENTENG/001',
            'jenis_hak_id' => $this->jenisHak->id,
            'luas' => 500,
            'alamat' => 'Jl. Gajah Mada No. 10',
            'wilayah_id' => $this->wilayah->id,
            'status_tanah_id' => $this->statusTanah->id,
            'pemilik_id' => $this->pemilik->id,
        ]);

        RiwayatKepemilikan::create([
            'tanah_id' => $this->tanah->id,
            'pemilik_lama_id' => null,
            'pemilik_baru_id' => $this->pemilik->id,
            'jenis_mutasi' => 'pendaftaran',
            'tanggal_mutasi' => now()->toDateString(),
            'keterangan' => 'Pendaftaran tanah awal.',
        ]);
    }

    /**
     * Guests are redirected to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Admin can view dashboard with populated stats and log table.
     */
    public function test_admin_can_view_dashboard_with_statistics(): void
    {
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'totalTanah',
            'totalPemilik',
            'totalLuas',
            'jenisHakDistribution',
            'wilayahDistribution',
            'recentActivities',
        ]);

        // Verify HTML contents
        $response->assertSee('Total Bidang Tanah');
        $response->assertSee('Total Pemilik');
        $response->assertSee('Total Luas Lahan');
        $response->assertSee('Sebaran Bidang Tanah per Wilayah Desa');
        $response->assertSee('Proporsi Jenis Hak Lahan');
        $response->assertSee('Aktivitas');
        $response->assertSee('Mutasi Terakhir');
        $response->assertSee('SERT/GENTENG/001');
    }
}
