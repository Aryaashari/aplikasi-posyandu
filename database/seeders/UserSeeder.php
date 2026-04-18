<?php

namespace Database\Seeders;

use App\Models\MdPosyandu;
use App\Models\MdUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a Posyandu
        $posyandu = MdPosyandu::create([
            'nama' => 'Posyandu Mawar Indah',
            'kode_posyandu' => 'PS-MAWAR-01',
            'provinsi' => 'DKI Jakarta',
            'kota' => 'Jakarta Selatan',
            'kecamatan' => 'Tebet',
            'kelurahan' => 'Manggarai Selatan',
            'alamat' => 'Jl. Mawar No. 123',
            'no_telp' => '021-12345678',
            'penanggung_jawab' => 'Ibu Siti Aminah',
        ]);

        // Create an Admin User
        MdUser::create([
            'id_posyandu' => $posyandu->id,
            'nama' => 'Admin Posyandu',
            'email' => 'admin@posyandu.id',
            'password' => Hash::make('password'),
            'no_telp' => '081234567890',
        ]);
    }
}
