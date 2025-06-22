<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada produk di database
        if (Product::count() == 0) {
            $this->command->info("Tidak ada produk, silakan buat produk terlebih dahulu.");
            return;
        }

        // Buat 3 pesanan 'pending'
        for ($i = 1; $i <= 3; $i++) {
            $this->createOrder("Pelanggan " . $i, "M" . $i, 'pending');
        }

        // Buat 2 pesanan 'processing'
        for ($i = 4; $i <= 5; $i++) {
            $this->createOrder("Pelanggan " . $i, "M" . $i, 'processing');
        }
    }

    private function createOrder($customerName, $tableNumber, $status)
    {
        // Ambil 1 sampai 3 produk secara acak
        $products = Product::inRandomOrder()->take(rand(1, 3))->get();
        $totalPrice = 0;

        // Buat order utama
        $order = Order::create([
            'customer_name' => $customerName,
            'table_number' => $tableNumber,
            'status' => $status,
            'total_price' => 0, // Akan diupdate nanti
        ]);

        // Buat item pesanan
        foreach ($products as $product) {
            $quantity = rand(1, 2);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
                'notes' => 'Catatan dummy untuk ' . $product->name,
            ]);
            $totalPrice += $product->price * $quantity;
        }

        // Update total harga di order utama
        $order->update(['total_price' => $totalPrice]);
    }
}