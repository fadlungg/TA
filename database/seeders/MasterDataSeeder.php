<?php

namespace Database\Seeders;

use App\Models\JenisHak;
use App\Models\Pemilik;
use App\Models\StatusTanah;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Jenis Hak
        $jenisHaks = [
            ['kode' => 'SHM', 'nama' => 'Sertifikat Hak Milik'],
            ['kode' => 'HGB', 'nama' => 'Sertifikat Hak Guna Bangunan'],
            ['kode' => 'HGU', 'nama' => 'Sertifikat Hak Guna Usaha'],
            ['kode' => 'HP', 'nama' => 'Hak Pakai'],
            ['kode' => 'Girik', 'nama' => 'Girik / Surat Adat'],
        ];

        foreach ($jenisHaks as $item) {
            JenisHak::updateOrCreate(['kode' => $item['kode']], $item);
        }

        // Seed Status Tanah
        $statusTanahs = [
            ['nama' => 'Aktif'],
            ['nama' => 'Sengketa'],
            ['nama' => 'Dijual'],
            ['nama' => 'Dalam Proses'],
        ];

        foreach ($statusTanahs as $item) {
            StatusTanah::updateOrCreate(['nama' => $item['nama']], $item);
        }

        // Seed Wilayah
        $wilayahs = [
            ['nama_kecamatan' => 'Tegalsari', 'nama_desa' => 'Tegalsari'],
            ['nama_kecamatan' => 'Tegalsari', 'nama_desa' => 'Kedungdoro'],
            ['nama_kecamatan' => 'Genteng', 'nama_desa' => 'Genteng'],
            ['nama_kecamatan' => 'Genteng', 'nama_desa' => 'Embong Kaliasin'],
            ['nama_kecamatan' => 'Bubutan', 'nama_desa' => 'Alun-Alun Contong'],
            ['nama_kecamatan' => 'Bubutan', 'nama_desa' => 'Bubutan'],
        ];

        foreach ($wilayahs as $item) {
            Wilayah::updateOrCreate([
                'nama_kecamatan' => $item['nama_kecamatan'],
                'nama_desa' => $item['nama_desa'],
            ], $item);
        }

        // Seed Pemilik
        $pemiliks = [
            [
                'nama' => 'Budi Santoso',
                'nik' => '3171012345670001',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1980-05-12',
                'alamat' => 'Jl. Kebon Jeruk No. 15, Jakarta Barat',
                'no_hp' => '081234567890',
                'email' => 'budi.santoso@email.com',
            ],
            [
                'nama' => 'Andi Wijaya',
                'nik' => '3171012345670002',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1985-08-20',
                'alamat' => 'Jl. Dharmahusada No. 45, Surabaya',
                'no_hp' => '082134567891',
                'email' => 'andi.wijaya@email.com',
            ],
            [
                'nama' => 'Siti Aminah',
                'nik' => '3171012345670003',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1990-11-05',
                'alamat' => 'Jl. Dago No. 102, Bandung',
                'no_hp' => '083134567892',
                'email' => 'siti.aminah@email.com',
            ],
        ];

        foreach ($pemiliks as $item) {
            Pemilik::updateOrCreate(['nik' => $item['nik']], $item);
        }
    }
}
