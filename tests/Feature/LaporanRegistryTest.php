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

class LaporanRegistryTest extends TestCase
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

        // Prepare lookup data
        $this->jenisHak = JenisHak::create(['kode' => 'SHM', 'nama' => 'Sertifikat Hak Milik']);
        $this->wilayah = Wilayah::create(['nama_kecamatan' => 'Genteng', 'nama_desa' => 'Genteng']);
        $this->statusTanah = StatusTanah::create(['nama' => 'Aktif']);

        // Prepare owner
        $this->pemilik = Pemilik::create([
            'nama' => 'Budi Santoso',
            'nik' => '1234567890123456',
            'tempat_lahir' => 'Banyuwangi',
            'tanggal_lahir' => '1985-05-12',
            'alamat' => 'Genteng',
            'no_hp' => '081234567890',
        ]);

        // Create Land
        $this->tanah = Tanah::create([
            'no_sertifikat' => 'SERT/GENTENG/001',
            'jenis_hak_id' => $this->jenisHak->id,
            'luas' => 500,
            'alamat' => 'Jl. Gajah Mada No. 10',
            'wilayah_id' => $this->wilayah->id,
            'status_tanah_id' => $this->statusTanah->id,
            'pemilik_id' => $this->pemilik->id,
        ]);

        // Create initial registration log
        RiwayatKepemilikan::create([
            'tanah_id' => $this->tanah->id,
            'pemilik_lama_id' => null,
            'pemilik_baru_id' => $this->pemilik->id,
            'jenis_mutasi' => 'pendaftaran',
            'tanggal_mutasi' => now()->toDateString(),
            'keterangan' => 'Pendaftaran tanah.',
        ]);
    }

    /**
     * Unauthenticated guests are redirected to login.
     */
    public function test_guest_cannot_access_laporan_routes(): void
    {
        $this->get(route('laporan.index'))->assertRedirect(route('login'));
        $this->get(route('laporan.export-tanah'))->assertRedirect(route('login'));
        $this->get(route('laporan.print-tanah'))->assertRedirect(route('login'));
    }

    /**
     * Admin can view reports index.
     */
    public function test_admin_can_view_laporan_index(): void
    {
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('laporan.index'));

        $response->assertStatus(200);
        $response->assertViewHasAll(['wilayahList', 'jenisHakList', 'statusTanahList', 'tanahData', 'rekapData', 'totalLuasOverall', 'mutasiData']);
        $response->assertSee('SERT/GENTENG/001');
    }

    /**
     * Admin can filter reports data.
     */
    public function test_admin_can_filter_laporan_data(): void
    {
        // Filter by matching wilayah
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('laporan.index', ['wilayah_id' => $this->wilayah->id]));

        $response->assertStatus(200);
        $this->assertCount(1, $response->viewData('tanahData'));

        // Filter by non-existent status ID (which should yield 0 bidang)
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('laporan.index', ['status_tanah_id' => 999]));

        $response->assertStatus(200);
        $this->assertCount(0, $response->viewData('tanahData'));
    }

    /**
     * Admin can download CSV exports.
     */
    public function test_admin_can_download_csv_exports(): void
    {
        // 1. Export Tanah CSV
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('laporan.export-tanah'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="laporan-bidang-tanah-'.now()->format('YmdHis').'.csv"');

        // 2. Export Rekap CSV
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('laporan.export-rekap'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // 3. Export Mutasi CSV
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('laporan.export-mutasi'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /**
     * Admin can access print-friendly layouts.
     */
    public function test_admin_can_view_print_layouts(): void
    {
        // 1. Print Tanah
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('laporan.print-tanah'));
        $response->assertStatus(200);
        $response->assertSee('Sipektatu - Sistem Kepemilikan Tanah');
        $response->assertSee('SERT/GENTENG/001');

        // 2. Print Rekap
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('laporan.print-rekap'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Rekapitulasi Luas Bidang Tanah Per Wilayah');

        // 3. Print Mutasi
        $response = $this->withSession(['admin_logged_in' => true, 'admin_username' => 'TestAdmin'])
            ->get(route('laporan.print-mutasi'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Riwayat Transaksi Mutasi Kepemilikan Tanah');
    }
}
