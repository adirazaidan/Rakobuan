<?php

namespace App\Http\Controllers\Customer;

use App\Events\NewOrderReceived;
use App\Events\TableStatusUpdated;
use App\Events\AvailableTablesUpdated;
use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CheckoutController extends Controller
{
    /**
     * Menyimpan pesanan dari keranjang ke database.
     */

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang Anda kosong.'], 400);
        }

        // Ambil dining_table_id dari session untuk digunakan setelah transaksi
        $diningTableId = session('dining_table_id');
        if (!$diningTableId) {
            return response()->json(['success' => false, 'message' => 'Sesi meja tidak valid.'], 400);
        }

        try {
            $order = DB::transaction(function () use ($cart, $diningTableId) {
                // Hitung total harga
                $totalPrice = 0;
                foreach ($cart as $item) {
                    $totalPrice += $item['price'] * $item['quantity'];
                }

                // Buat record order baru
                $newOrder = Order::create([
                    'dining_table_id' => $diningTableId,
                    'session_id'      => session()->getId(),
                    'customer_name'   => session('customer_name'),
                    'table_number'    => session('table_number'),
                    'total_price'     => $totalPrice,
                    'status'          => 'pending',
                ]);

                // Buat record untuk setiap item pesanan
                foreach ($cart as $id => $details) {
                    OrderItem::create([
                        'order_id'    => $newOrder->id,
                        'product_id'  => $id, // Menggunakan key dari array sebagai product_id
                        'quantity'    => $details['quantity'],
                        'price'       => $details['price'],
                        'notes'       => $details['notes'] ?? null,
                    ]);
                }

                return $newOrder;
            });

            // Setelah transaksi berhasil:

            // 1. Picu event untuk notifikasi pesanan baru (global)
            NewOrderReceived::dispatch($order);

            // 2. Picu event untuk update dasbor meja (mengirim ID meja)
            TableStatusUpdated::dispatch($diningTableId);
            
            // 3. Picu event untuk update dropdown meja pelanggan lain
            AvailableTablesUpdated::dispatch();

            // 4. Hapus keranjang dari sesi
            session()->forget('cart');

            // 5. Kembalikan respon sukses untuk AJAX agar bisa redirect
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'redirect_url' => route('order.success', ['order' => $order->id])
            ]);

        } catch (\Exception $e) {
            Log::error("Gagal membuat pesanan: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function success(Order $order)
    {
        return view('customer.checkout.success', compact('order'));
    }
}