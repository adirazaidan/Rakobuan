<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Events\NewOrderReceived;
use App\Events\TableStatusUpdated; 
use App\Models\DiningTable;  

use Illuminate\Support\Facades\DB; 

class CheckoutController extends Controller
{
    /**
     * Menyimpan pesanan dari keranjang ke database.
     */
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.menu.index')->with('error', 'Keranjang Anda kosong!');
        }

        try {
            // Mulai transaksi database
            $order = DB::transaction(function () use ($cart) {
                // Hitung total harga
                $totalPrice = 0;
                foreach ($cart as $details) {
                    $totalPrice += $details['price'] * $details['quantity'];
                }

                // Buat record di tabel 'orders'
                $newOrder = Order::create([
                    'dining_table_id' => session('dining_table_id'),
                    'customer_name' => session('customer_name'),
                    'table_number' => session('table_number'),
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                ]);

                // Buat record di tabel 'order_items'
                foreach ($cart as $id => $details) {
                    OrderItem::create([
                        'order_id' => $newOrder->id,
                        'product_id' => $id,
                        'quantity' => $details['quantity'],
                        'price' => $details['price'],
                        'notes' => $details['notes'],
                    ]);
                }
                
                // Kembalikan order yang baru dibuat dari transaksi
                return $newOrder;
            });

            // =================================================================
            // ===== LOGIKA EVENT DIPINDAHKAN KE SINI (SETELAH TRANSAKSI) =====
            // =================================================================
            
            // Picu event notifikasi suara
            NewOrderReceived::dispatch($order);

            // Ambil data meja yang terkait dengan pesanan
            $table = DiningTable::find(session('dining_table_id'));
            if ($table) {
                // Kirim event untuk update tampilan visual meja di admin panel
                // Sekarang, $table->activeOrder dijamin sudah ada karena transaksi telah selesai.
                TableStatusUpdated::dispatch($table->id);
            }
            
            // Jika transaksi berhasil, hapus keranjang dari session
            session()->forget('cart');

            // Redirect ke halaman sukses
            return redirect()->route('order.success', ['order' => $order->id]);

        } catch (\Exception $e) {
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