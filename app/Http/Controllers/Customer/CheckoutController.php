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
use App\Models\DiningTable;
use App\Models\Setting; 



class CheckoutController extends Controller
{

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang Anda kosong.'], 400);
        }

        $diningTableId = session('dining_table_id');
        // Temukan meja beserta relasi outlet-nya
        $table = DiningTable::with('outlet')->find($diningTableId);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Sesi meja tidak valid.'], 400);
        }

        // --- PENGECEKAN BARU DI SINI ---
        // Periksa apakah outlet dari meja ini sedang menerima pesanan
        $isAcceptingOrders = Setting::where('key', 'accepting_orders')->first()->value ?? 'true';
        if ($isAcceptingOrders !== 'true') {
            return response()->json([
                'success' => false, 
                'message' => 'Mohon maaf, saat ini kami sudah tidak menerima pesanan baru.'
            ], 403);
        }
        $order = null; 
        try {
            $order = DB::transaction(function () use ($cart, $diningTableId) {
                // ... sisa logika transaksi Anda (tidak berubah) ...
                foreach ($cart as $id => $details) {
                    $product = Product::lockForUpdate()->find($id); 
                    if (!$product || $product->stock < $details['quantity']) {
                        throw new \Exception("Stok untuk produk '{$details['name']}' tidak mencukupi.");
                    }
                }
                $totalPrice = 0;
                foreach ($cart as $item) {
                    $totalPrice += $item['price'] * $item['quantity'];
                }
                $newOrder = Order::create([
                    'dining_table_id' => $diningTableId,
                    'session_id'      => session()->getId(),
                    'customer_name'   => session('customer_name'),
                    'table_number'    => session('table_number'),
                    'total_price'     => $totalPrice,
                    'status'          => 'pending',
                ]);
                foreach ($cart as $id => $details) {
                    OrderItem::create([
                        'order_id'   => $newOrder->id,
                        'product_id' => $id,
                        'quantity'   => $details['quantity'],
                        'price'      => $details['price'],
                        'notes'      => $details['notes'] ?? null,
                    ]);
                    $product = Product::find($id); 
                    $product->decrement('stock', $details['quantity']);
                    if ($product->stock <= 0) {
                        $product->is_available = false;
                        $product->save();
                    }
                }
                return $newOrder;
            }); 
            
            NewOrderReceived::dispatch($order);
            TableStatusUpdated::dispatch($diningTableId);
            AvailableTablesUpdated::dispatch();
            foreach ($cart as $id => $details) {
                $product = Product::find($id);
                ProductStockUpdated::dispatch($product->id, $product->stock, $product->is_available);
            }

            session()->forget('cart');
            
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'redirect_url' => route('order.status')
            ]);

        } catch (\Exception $e) {
            Log::error("Gagal membuat pesanan: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422); 
        }
    }

    public function success(Order $order)
    {
        return view('customer.checkout.success', compact('order'));
    }


}