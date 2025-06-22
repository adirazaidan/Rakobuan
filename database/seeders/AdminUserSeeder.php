<?php

namespace Database\Seeders;
use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
        {
            User::create([
                'name' => 'Admin Rakobuan',
                'email' => 'admin@rakobuan.com',
                'password' => Hash::make('rakobuanjaya123123'), // Ganti dengan password yang kuat
            ]);
        }
}
