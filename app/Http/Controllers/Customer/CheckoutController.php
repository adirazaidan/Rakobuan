<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Import DB Facade

class CheckoutController extends Controller
{
    /**
     * Menyimpan pesanan dari keranjang ke database.
     */
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        // Validasi jika keranjang kosong
        if (empty($cart)) {
            return redirect()->route('customer.menu.index')->with('error', 'Keranjang Anda kosong!');
        }

        try {
            // Mulai transaksi database
            $order = DB::transaction(function () use ($cart) {
                // 1. Hitung ulang total harga di sisi server
                $totalPrice = 0;
                foreach ($cart as $details) {
                    $totalPrice += $details['price'] * $details['quantity'];
                }

                // 2. Buat record di tabel 'orders'
                $order = Order::create([
                    'customer_name' => session('customer_name'),
                    'table_number' => session('table_number'),
                    'total_price' => $totalPrice,
                    'status' => 'pending', // Status awal saat pesanan masuk
                ]);

                // 3. Buat record di tabel 'order_items' untuk setiap item
                foreach ($cart as $id => $details) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $id,
                        'quantity' => $details['quantity'],
                        'price' => $details['price'], // Simpan harga saat itu
                        'notes' => $details['notes'],
                    ]);
                }

                return $order;
            });

            // 4. Jika transaksi berhasil, hapus keranjang dari session
            session()->forget('cart');

            // 5. Redirect ke halaman sukses dengan membawa ID pesanan
            return redirect()->route('order.success', ['order' => $order->id]);

        } catch (\Exception $e) {
            // Jika terjadi error selama transaksi, kembalikan ke keranjang dengan pesan error
            return redirect()->route('cart.index')->with('error', 'Gagal membuat pesanan, silakan coba lagi. Error: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman sukses/resi setelah checkout.
     */
    public function success(Order $order)
    {
        // Gunakan Route Model Binding untuk mengambil data order secara otomatis
        // Pastikan pelanggan hanya bisa melihat ordernya sendiri (bisa ditambahkan validasi nanti)
        return view('customer.checkout.success', compact('order'));
    }
}