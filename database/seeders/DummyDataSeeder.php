<?php

namespace Database\Seeders;

use App\Models\JenisHak;
use App\Models\Pemilik;
use App\Models\StatusTanah;
use App\Models\Tanah;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Jenis Hak
        $jenisHaks = [
            ['kode' => 'SHM', 'nama' => 'Sertifikat Hak Milik'],
            ['kode' => 'HGB', 'nama' => 'Sertifikat Hak Guna Bangunan'],
            ['kode' => 'HGU', 'nama' => 'Sertifikat Hak Guna Usaha'],
            ['kode' => 'HP', 'nama' => 'Hak Pakai'],
            ['kode' => 'Girik', 'nama' => 'Girik / Buku Letter C'],
        ];

        foreach ($jenisHaks as $item) {
            JenisHak::updateOrCreate(['kode' => $item['kode']], $item);
        }

        // 2. Seed Status Tanah
        $statusTanahs = [
            ['nama' => 'Aktif'],
            ['nama' => 'Sengketa'],
            ['nama' => 'Dijual'],
            ['nama' => 'Dalam Proses'],
        ];

        foreach ($statusTanahs as $item) {
            StatusTanah::updateOrCreate(['nama' => $item['nama']], $item);
        }

        // 3. Seed Wilayah (Desa Tunggorono, Kutoarjo)
        $wilayahs = [
            [
                'nama_kecamatan' => 'Kutoarjo',
                'nama_desa' => 'Tunggorono',
                'nama_dusun' => 'Dusun Krajan',
                'no_rw' => '01',
                'no_rt' => '01',
            ],
            [
                'nama_kecamatan' => 'Kutoarjo',
                'nama_desa' => 'Tunggorono',
                'nama_dusun' => 'Dusun Krajan',
                'no_rw' => '01',
                'no_rt' => '02',
            ],
            [
                'nama_kecamatan' => 'Kutoarjo',
                'nama_desa' => 'Tunggorono',
                'nama_dusun' => 'Dusun Karangrejo',
                'no_rw' => '02',
                'no_rt' => '01',
            ],
            [
                'nama_kecamatan' => 'Kutoarjo',
                'nama_desa' => 'Tunggorono',
                'nama_dusun' => 'Dusun Karangrejo',
                'no_rw' => '02',
                'no_rt' => '02',
            ],
            [
                'nama_kecamatan' => 'Kutoarjo',
                'nama_desa' => 'Tunggorono',
                'nama_dusun' => 'Dusun Tunggorono',
                'no_rw' => '03',
                'no_rt' => '01',
            ],
            [
                'nama_kecamatan' => 'Kutoarjo',
                'nama_desa' => 'Tunggorono',
                'nama_dusun' => 'Dusun Tunggorono',
                'no_rw' => '03',
                'no_rt' => '02',
            ],
        ];

        $seededWilayah = [];
        foreach ($wilayahs as $item) {
            $seededWilayah[] = Wilayah::updateOrCreate([
                'nama_kecamatan' => $item['nama_kecamatan'],
                'nama_desa' => $item['nama_desa'],
                'nama_dusun' => $item['nama_dusun'],
                'no_rw' => $item['no_rw'],
                'no_rt' => $item['no_rt'],
            ], $item);
        }

        // 4. Seed Pemilik (Warga Tunggorono)
        $pemiliks = [
            [
                'nama' => 'Sri Utami',
                'nik' => '3306051208900001',
                'tempat_lahir' => 'Purworejo',
                'tanggal_lahir' => '1990-08-12',
                'alamat' => 'RT 01/RW 01, Dusun Krajan, Tunggorono',
                'no_hp' => '081234567801',
                'email' => 'sri.utami@email.com',
            ],
            [
                'nama' => 'Sugeng Purwanto',
                'nik' => '3306051510780002',
                'tempat_lahir' => 'Kutoarjo',
                'tanggal_lahir' => '1978-10-15',
                'alamat' => 'RT 01/RW 02, Dusun Krajan, Tunggorono',
                'no_hp' => '082134567802',
                'email' => 'sugeng.p@email.com',
            ],
            [
                'nama' => 'Hartono',
                'nik' => '3306050202820003',
                'tempat_lahir' => 'Purworejo',
                'tanggal_lahir' => '1982-02-02',
                'alamat' => 'RT 01/RW 01, Dusun Karangrejo, Tunggorono',
                'no_hp' => '083134567803',
                'email' => 'hartono@email.com',
            ],
            [
                'nama' => 'Pemerintah Desa Tunggorono',
                'nik' => '3306050000000001',
                'tempat_lahir' => 'Purworejo',
                'tanggal_lahir' => '1945-08-17',
                'alamat' => 'Kantor Kepala Desa Tunggorono, Kutoarjo',
                'no_hp' => '0275641234',
                'email' => 'pemdes.tunggorono@purworejokab.go.id',
            ],
            [
                'nama' => 'Kusnan (Mbah Kus)',
                'nik' => '3306050909550005',
                'tempat_lahir' => 'Purworejo',
                'tanggal_lahir' => '1955-09-09',
                'alamat' => 'RT 02/RW 03, Dusun Tunggorono',
                'no_hp' => '089876543210',
            ],
        ];

        $seededPemilik = [];
        foreach ($pemiliks as $item) {
            $seededPemilik[] = Pemilik::updateOrCreate(['nik' => $item['nik']], $item);
        }

        // Resolve status IDs
        $statusAktif = StatusTanah::where('nama', 'Aktif')->first()->id;
        $statusSengketa = StatusTanah::where('nama', 'Sengketa')->first()->id;

        // Resolve jenis hak IDs
        $shmId = JenisHak::where('kode', 'SHM')->first()->id;
        $girikId = JenisHak::where('kode', 'Girik')->first()->id;
        $hpId = JenisHak::where('kode', 'HP')->first()->id;

        // 5. Seed Tanah
        $tanahData = [
            [
                'no_sertifikat' => 'SHM-TGR-001',
                'no_letter_c' => 'C 23',
                'no_persil' => 'Persil 10',
                'klas_tanah' => 'D.I',
                'status_bengkok' => null,
                'jenis_hak_id' => $shmId,
                'luas' => 450,
                'alamat' => 'Pekarangan Krajan RT 01/RW 01',
                'wilayah_id' => $seededWilayah[0]->id, // Krajan RT 01/RW 01
                'status_tanah_id' => $statusAktif,
                'pemilik_id' => $seededPemilik[0]->id, // Sri Utami
                'latitude' => -7.712345,
                'longitude' => 109.912345,
            ],
            [
                'no_sertifikat' => 'LETTER-C-TGR-045',
                'no_letter_c' => 'C 45',
                'no_persil' => 'Persil 22',
                'klas_tanah' => 'S.II',
                'status_bengkok' => null,
                'jenis_hak_id' => $girikId,
                'luas' => 1800,
                'alamat' => 'Sawah Karangrejo RT 01/RW 02',
                'wilayah_id' => $seededWilayah[2]->id, // Karangrejo RT 01/RW 02
                'status_tanah_id' => $statusAktif,
                'pemilik_id' => $seededPemilik[1]->id, // Sugeng Purwanto
                'latitude' => -7.713500,
                'longitude' => 109.914000,
            ],
            [
                'no_sertifikat' => 'LETTER-C-TGR-089',
                'no_letter_c' => 'C 89',
                'no_persil' => 'Persil 35',
                'klas_tanah' => 'S.I',
                'status_bengkok' => null,
                'jenis_hak_id' => $girikId,
                'luas' => 3200,
                'alamat' => 'Sawah Blok Tunggorono RT 02/RW 03',
                'wilayah_id' => $seededWilayah[5]->id, // Tunggorono RT 02/RW 03
                'status_tanah_id' => $statusSengketa,
                'pemilik_id' => $seededPemilik[2]->id, // Hartono
                'latitude' => -7.714200,
                'longitude' => 109.915500,
            ],
            [
                'no_sertifikat' => 'HP-TGR-BENGKOK-01',
                'no_letter_c' => 'C 1',
                'no_persil' => 'Persil 1a',
                'klas_tanah' => 'S.I',
                'status_bengkok' => 'Bengkok Kepala Desa',
                'jenis_hak_id' => $hpId,
                'luas' => 8500,
                'alamat' => 'Bengkok Kades Blok Krajan RT 02/RW 01',
                'wilayah_id' => $seededWilayah[1]->id, // Krajan RT 02/RW 01
                'status_tanah_id' => $statusAktif,
                'pemilik_id' => $seededPemilik[3]->id, // Pemerintah Desa
                'latitude' => -7.711100,
                'longitude' => 109.911100,
            ],
            [
                'no_sertifikat' => 'HP-TGR-TKD-02',
                'no_letter_c' => 'C 2',
                'no_persil' => 'Persil 2b',
                'klas_tanah' => 'D.II',
                'status_bengkok' => 'Tanah Kas Desa (TKD)',
                'jenis_hak_id' => $hpId,
                'luas' => 12500,
                'alamat' => 'Kas Desa Blok Karangrejo RT 02/RW 02',
                'wilayah_id' => $seededWilayah[3]->id, // Karangrejo RT 02/RW 02
                'status_tanah_id' => $statusAktif,
                'pemilik_id' => $seededPemilik[3]->id, // Pemerintah Desa
                'latitude' => -7.715500,
                'longitude' => 109.916600,
            ],
        ];

        DB::transaction(function () use ($tanahData, $seededPemilik) {
            foreach ($tanahData as $t) {
                $tanah = Tanah::updateOrCreate(['no_sertifikat' => $t['no_sertifikat']], $t);

                // Clear existing history logs if re-running
                $tanah->riwayatKepemilikan()->delete();

                // Create initial registration log
                $tanah->riwayatKepemilikan()->create([
                    'pemilik_lama_id' => null,
                    'pemilik_baru_id' => $t['pemilik_id'],
                    'jenis_mutasi' => 'pendaftaran',
                    'tanggal_mutasi' => '2020-01-01',
                    'keterangan' => 'Pendaftaran tanah awal di sistem desa.',
                ]);
            }

            // Create a Jual Beli mutation for first plot
            // Sri Utami bought SHM-TGR-001 from Mbah Kus in 2024
            $plot1 = Tanah::where('no_sertifikat', 'SHM-TGR-001')->first();
            if ($plot1) {
                $plot1->riwayatKepemilikan()->create([
                    'pemilik_lama_id' => $seededPemilik[4]->id, // Mbah Kus
                    'pemilik_baru_id' => $seededPemilik[0]->id, // Sri Utami
                    'jenis_mutasi' => 'jual_beli',
                    'tanggal_mutasi' => '2024-05-15',
                    'keterangan' => 'Transaksi jual beli dihadapan Akta PPAT No 12/2024.',
                ]);
            }

            // Create a Waris mutation for second plot
            // Sugeng Purwanto inherited LETTER-C-TGR-045 from Hartono in 2025
            $plot2 = Tanah::where('no_sertifikat', 'LETTER-C-TGR-045')->first();
            if ($plot2) {
                $plot2->riwayatKepemilikan()->create([
                    'pemilik_lama_id' => $seededPemilik[2]->id, // Hartono
                    'pemilik_baru_id' => $seededPemilik[1]->id, // Sugeng Purwanto
                    'jenis_mutasi' => 'waris',
                    'tanggal_mutasi' => '2025-02-10',
                    'keterangan' => 'Pewarisan hak tanah yasan keluarga.',
                ]);
            }
        });
    }
}
