<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\Jadwal;
use App\Models\CatatanHarian;
use App\Models\Presensi;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ========== 1. ROLES ==========
        $roles = ['admin', 'guru', 'wali_kelas'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // ========== 2. USERS ==========
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.test'],
            [
                'name' => 'Admin Sekolah',
                'password' => Hash::make('password'),
                'kode_guru' => 'ADM001',
            ]
        );
        $admin->assignRole('admin');

        // Guru 1 - Mengajar Matematika di kelas 10A dan 10B
        $guru1 = User::firstOrCreate(
            ['email' => 'budi.guru@school.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'kode_guru' => 'GR001',
            ]
        );
        $guru1->assignRole('guru');

        // Guru 2 - Mengajar Bahasa Indonesia di kelas 10A, 10B, 10C
        $guru2 = User::firstOrCreate(
            ['email' => 'siti.guru@school.test'],
            [
                'name' => 'Siti Rahayu',
                'password' => Hash::make('password'),
                'kode_guru' => 'GR002',
            ]
        );
        $guru2->assignRole('guru');

        // Wali Kelas 10A (juga guru IPA)
        $walikelas1 = User::firstOrCreate(
            ['email' => 'andi.walikelas@school.test'],
            [
                'name' => 'Andi Wijaya',
                'password' => Hash::make('password'),
                'kode_guru' => 'WK001',
            ]
        );
        $walikelas1->assignRole(['guru', 'wali_kelas']);

        // Wali Kelas 10B (juga guru IPS)
        $walikelas2 = User::firstOrCreate(
            ['email' => 'dewi.walikelas@school.test'],
            [
                'name' => 'Dewi Lestari',
                'password' => Hash::make('password'),
                'kode_guru' => 'WK002',
            ]
        );
        $walikelas2->assignRole(['guru', 'wali_kelas']);

        // Wali Kelas 10C (bukan guru, hanya wali kelas)
        $walikelas3 = User::firstOrCreate(
            ['email' => 'rudi.walikelas@school.test'],
            [
                'name' => 'Rudi Hartono',
                'password' => Hash::make('password'),
                'kode_guru' => 'WK003',
            ]
        );
        $walikelas3->assignRole('wali_kelas');

        // ========== 3. MATA PELAJARAN ==========
        $mapel = [];
        $mapelData = ['Matematika', 'Bahasa Indonesia', 'IPA', 'IPS', 'Bahasa Inggris'];
        foreach ($mapelData as $nama) {
            $mapel[$nama] = MataPelajaran::firstOrCreate(
                ['nama' => $nama],
                ['kode_mapel' => MataPelajaran::generateKodeMapel($nama)]
            );
        }

        // ========== 4. KELAS ==========
        $kelas10A = Kelas::firstOrCreate(
            ['nama' => '10A'],
            ['jumlah_murid' => 30, 'wali_kelas_id' => $walikelas1->id]
        );
        $kelas10B = Kelas::firstOrCreate(
            ['nama' => '10B'],
            ['jumlah_murid' => 28, 'wali_kelas_id' => $walikelas2->id]
        );
        $kelas10C = Kelas::firstOrCreate(
            ['nama' => '10C'],
            ['jumlah_murid' => 25, 'wali_kelas_id' => $walikelas3->id]
        );

        // Map kelas by nama
        $kelasMap = [
            '10A' => $kelas10A,
            '10B' => $kelas10B,
            '10C' => $kelas10C,
        ];

        // ========== 5. SISWA ==========
        $siswaNama = [
            '10A' => ['Ahmad Fauzi', 'Budi Prasetyo', 'Citra Dewi', 'Dian Permata', 'Eko Saputra'],
            '10B' => ['Fitri Handayani', 'Galih Pratama', 'Hana Safitri', 'Ivan Kurniawan', 'Joko Widodo'],
            '10C' => ['Kartika Sari', 'Lukman Hakim', 'Maya Anggraini', 'Nanda Putra', 'Olivia Rahma'],
        ];

        $siswaByKelas = [];
        foreach ($siswaNama as $kelasNama => $names) {
            $kelasModel = $kelasMap[$kelasNama];
            $siswaByKelas[$kelasNama] = [];
            foreach ($names as $nama) {
                $siswa = Siswa::firstOrCreate(
                    ['nama' => $nama, 'kelas_id' => $kelasModel->id]
                );
                $siswaByKelas[$kelasNama][] = $siswa;
            }
        }

        // ========== 6. JADWAL ==========
        $jadwalData = [
            // Guru 1 (Budi) - Matematika di 10A dan 10B
            ['kelas' => $kelas10A, 'mapel' => $mapel['Matematika'], 'guru' => $guru1, 'hari' => 'Senin', 'jam' => 1],
            ['kelas' => $kelas10B, 'mapel' => $mapel['Matematika'], 'guru' => $guru1, 'hari' => 'Selasa', 'jam' => 2],
            
            // Guru 2 (Siti) - Bahasa Indonesia di semua kelas
            ['kelas' => $kelas10A, 'mapel' => $mapel['Bahasa Indonesia'], 'guru' => $guru2, 'hari' => 'Rabu', 'jam' => 1],
            ['kelas' => $kelas10B, 'mapel' => $mapel['Bahasa Indonesia'], 'guru' => $guru2, 'hari' => 'Rabu', 'jam' => 3],
            ['kelas' => $kelas10C, 'mapel' => $mapel['Bahasa Indonesia'], 'guru' => $guru2, 'hari' => 'Kamis', 'jam' => 1],
            
            // Wali Kelas 1 (Andi) - IPA di 10A (kelas sendiri)
            ['kelas' => $kelas10A, 'mapel' => $mapel['IPA'], 'guru' => $walikelas1, 'hari' => 'Senin', 'jam' => 3],
            
            // Wali Kelas 2 (Dewi) - IPS di 10B dan 10C
            ['kelas' => $kelas10B, 'mapel' => $mapel['IPS'], 'guru' => $walikelas2, 'hari' => 'Kamis', 'jam' => 2],
            ['kelas' => $kelas10C, 'mapel' => $mapel['IPS'], 'guru' => $walikelas2, 'hari' => 'Jumat', 'jam' => 1],
        ];

        $jadwals = [];
        foreach ($jadwalData as $data) {
            $jadwal = Jadwal::firstOrCreate([
                'kelas_id' => $data['kelas']->id,
                'mapel_id' => $data['mapel']->id,
                'guru_id' => $data['guru']->id,
                'hari' => $data['hari'],
                'jam_ke' => $data['jam'],
            ], [
                'kode_guru' => $data['guru']->kode_guru,
            ]);
            $jadwals[] = $jadwal;
        }

        // ========== 7. CATATAN HARIAN ==========
        // Catatan oleh Guru Budi (Matematika 10A)
        $catatan1 = CatatanHarian::firstOrCreate([
            'jadwal_id' => $jadwals[0]->id,
            'tanggal' => now()->subDays(2)->format('Y-m-d'),
        ], [
            'user_id' => $guru1->id,
            'materi' => 'Persamaan Linear Satu Variabel',
            'catatan' => 'Siswa antusias mengikuti pelajaran',
            'approved_at' => now(),
            'approved_by' => $walikelas1->id,
        ]);

        // Catatan oleh Guru Siti (Bahasa Indonesia 10B) - belum approved
        $catatan2 = CatatanHarian::firstOrCreate([
            'jadwal_id' => $jadwals[3]->id,
            'tanggal' => now()->subDays(1)->format('Y-m-d'),
        ], [
            'user_id' => $guru2->id,
            'materi' => 'Teks Narasi',
            'catatan' => 'Ada 2 siswa yang belum mengumpulkan tugas',
        ]);

        // Catatan oleh Wali Kelas Andi (IPA 10A)
        $catatan3 = CatatanHarian::firstOrCreate([
            'jadwal_id' => $jadwals[5]->id,
            'tanggal' => now()->format('Y-m-d'),
        ], [
            'user_id' => $walikelas1->id,
            'materi' => 'Struktur Atom',
            'catatan' => 'Praktikum berjalan lancar',
        ]);

        // Catatan oleh Wali Kelas Dewi (IPS 10C - bukan kelas yang diwalikan)
        $catatan4 = CatatanHarian::firstOrCreate([
            'jadwal_id' => $jadwals[7]->id,
            'tanggal' => now()->format('Y-m-d'),
        ], [
            'user_id' => $walikelas2->id,
            'materi' => 'Sejarah Indonesia',
            'catatan' => 'Diskusi kelompok berjalan baik',
        ]);

        // ========== 8. PRESENSI ==========
        $catatans = [$catatan1, $catatan2, $catatan3, $catatan4];
        $kelasFromJadwal = [$kelas10A, $kelas10B, $kelas10A, $kelas10C];
        
        foreach ($catatans as $idx => $catatan) {
            $kelasNama = $kelasFromJadwal[$idx]->nama;
            if (isset($siswaByKelas[$kelasNama])) {
                foreach ($siswaByKelas[$kelasNama] as $i => $siswa) {
                    $status = $i === 0 ? 'izin' : ($i === 1 ? 'sakit' : 'hadir');
                    Presensi::firstOrCreate([
                        'catatan_harian_id' => $catatan->id,
                        'siswa_id' => $siswa->id,
                    ], [
                        'status' => $status,
                        'keterangan' => $status !== 'hadir' ? 'Keterangan ' . $status : null,
                    ]);
                }
            }
        }

        $this->command->info('✅ Dummy data berhasil dibuat!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@school.test', 'password'],
                ['Guru (Matematika)', 'budi.guru@school.test', 'password'],
                ['Guru (B. Indo)', 'siti.guru@school.test', 'password'],
                ['Wali Kelas 10A + Guru IPA', 'andi.walikelas@school.test', 'password'],
                ['Wali Kelas 10B + Guru IPS', 'dewi.walikelas@school.test', 'password'],
                ['Wali Kelas 10C (only)', 'rudi.walikelas@school.test', 'password'],
            ]
        );
    }
}
