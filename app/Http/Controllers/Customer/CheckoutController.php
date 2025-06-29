<?php

namespace App\Http\Controllers\Customer;

use App\Events\NewOrderReceived;
use App\Events\TableStatusUpdated;
use App\Events\AvailableTablesUpdated;
use App\Http\Controllers\Controller;
use App\Events\ProductStockUpdated;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class CheckoutController extends Controller
{

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang Anda kosong.'], 400);
        }
        $diningTableId = session('dining_table_id');
        if (!$diningTableId) {
            return response()->json(['success' => false, 'message' => 'Sesi meja tidak valid.'], 400);
        }

        $order = null; // Definisikan di luar try-catch

        try {
            $order = DB::transaction(function () use ($cart, $diningTableId) {
                // --- 3. LOGIKA VALIDASI STOK (SEBELUM MEMBUAT ORDER) ---
                foreach ($cart as $id => $details) {
                    $product = Product::lockForUpdate()->find($id); // Kunci baris untuk mencegah race condition
                    if (!$product || $product->stock < $details['quantity']) {
                        // Throw exception untuk membatalkan transaksi & kirim error
                        throw new \Exception("Stok untuk produk '{$details['name']}' tidak mencukupi.");
                    }
                }

                // --- Kalkulasi Total Harga ---
                $totalPrice = 0;
                foreach ($cart as $item) {
                    $totalPrice += $item['price'] * $item['quantity'];
                }

                // --- Membuat Order (Logika lama Anda, tetap di sini) ---
                $newOrder = Order::create([
                    'dining_table_id' => $diningTableId,
                    'session_id'      => session()->getId(),
                    'customer_name'   => session('customer_name'),
                    'table_number'    => session('table_number'),
                    'total_price'     => $totalPrice,
                    'status'          => 'pending',
                ]);

                // --- 4. MEMBUAT ORDER ITEM & MENGURANGI STOK ---
                foreach ($cart as $id => $details) {
                    OrderItem::create([
                        'order_id'   => $newOrder->id,
                        'product_id' => $id,
                        'quantity'   => $details['quantity'],
                        'price'      => $details['price'],
                        'notes'      => $details['notes'] ?? null,
                    ]);

                    // Mengurangi stok produk
                    $product = Product::find($id); // Tidak perlu lock lagi karena sudah divalidasi
                    $product->decrement('stock', $details['quantity']);

                    // Update status is_available jika stok habis
                    if ($product->stock <= 0) {
                        $product->is_available = false;
                        $product->save();
                    }
                }

                return $newOrder;
            }); // Akhir dari DB::transaction

            // --- 5. DISPATCH EVENT (SETELAH TRANSAKSI SUKSES) ---
            NewOrderReceived::dispatch($order);
            TableStatusUpdated::dispatch($diningTableId);
            AvailableTablesUpdated::dispatch();

            // Dispatch event update stok untuk setiap item di keranjang
            foreach ($cart as $id => $details) {
                $product = Product::find($id);
                ProductStockUpdated::dispatch($product->id, $product->stock, $product->is_available);
            }

            session()->forget('cart');
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'redirect_url' => route('order.success', ['order' => $order->id])
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal membuat pesanan: " . $e->getMessage());
            return response()->json([
                'success' => false,
                // Mengirim pesan error yang lebih spesifik ke pelanggan
                'message' => $e->getMessage()
            ], 422); // Gunakan status 422 untuk error validasi
        }
    }

    public function success(Order $order)
    {
        return view('customer.checkout.success', compact('order'));
    }


}