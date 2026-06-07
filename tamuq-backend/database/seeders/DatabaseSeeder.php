<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nama_lengkap' => 'SupAdmin1',
                'nomor_induk'  => '10001',
                'email'        => null,
                'prodi'        => 'PTIK',
                'role'         => 'superadmin',
                'password_hash'=> Hash::make('SupAdmn123'),
                'status_akun'  => 'aktif',
                'created_at'   => Carbon::now(), 'updated_at' => Carbon::now()
            ],
            [
                'nama_lengkap' => 'Admin1',
                'nomor_induk'  => '20001',
                'email'        => null,
                'prodi'        => 'PTIK',
                'role'         => 'staff',
                'password_hash'=> Hash::make('Admn123'),
                'status_akun'  => 'aktif',
                'created_at'   => Carbon::now(), 'updated_at' => Carbon::now()
            ],
            [
                'nama_lengkap' => 'Muhammad Ikram',
                'nomor_induk'  => '210209502028',
                'email'        => null,
                'prodi'        => 'PTIK',
                'role'         => 'pengunjung',
                'password_hash'=> Hash::make('password123'),
                'status_akun'  => 'aktif',
                'created_at'   => Carbon::now(), 'updated_at' => Carbon::now()
            ],
        ];

        DB::table('users')->insert($users);

        DB::table('buku')->insert([
            [
                'kode_buku' => 'B001',
                'judul' => 'Belajar Laravel 10',
                'penulis' => 'Taylor Otwell',
                'kategori' => 'Teknologi',
                'tempat' => 'Jakarta', 
                'tahun_buku' => 2023,
                'jumlah_tersedia' => 10,
                'created_at'   => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'kode_buku' => 'B002',
                'judul' => 'Vue.js untuk Pemula',
                'penulis' => 'Evan You',
                'kategori' => 'Teknologi',
                'tempat' => 'Bandung', 
                'tahun_buku' => 2022,
                'jumlah_tersedia' => 5,
                'created_at'   => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);

        DB::table('settings')->insert([
            'key' => 'library_status',
            'value' => 'tutup',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}