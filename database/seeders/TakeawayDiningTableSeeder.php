<?php

namespace Database\Seeders;

use App\Models\DiningTable;
use Illuminate\Database\Seeder;

class TakeawayDiningTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Periksa apakah meja 'Takeaway' sudah ada
        $existingTable = DiningTable::where('name', 'Takeaway')->first();

        // Jika belum ada, buat meja baru
        if (!$existingTable) {
            DiningTable::create([
                'name' => 'Takeaway',
                'notes' => 'Meja khusus untuk pesanan dibawa pulang.',
                'location' => 'Takeaway',
                'is_locked' => true, // Biasanya meja takeaway terkunci
            ]);
        }
    }
}