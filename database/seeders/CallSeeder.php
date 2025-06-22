<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Call;

class CallSeeder extends Seeder
{
    public function run(): void
    {
        // Panggilan yang masih menunggu (pending)
        Call::create([
            'customer_name' => 'Budi Santoso',
            'table_number' => 'M5',
            'notes' => 'Minta tambahan sendok dan garpu.',
            'status' => 'pending'
        ]);

        Call::create([
            'customer_name' => 'Citra Lestari',
            'table_number' => 'V2',
            'notes' => 'Tisu habis, tolong diisi ulang.',
            'status' => 'pending'
        ]);

        // Panggilan yang sudah ditangani (handled)
        Call::create([
            'customer_name' => 'Agus Wijaya',
            'table_number' => 'M3',
            'notes' => 'Mau tambah pesanan.',
            'status' => 'handled'
        ]);
    }
}