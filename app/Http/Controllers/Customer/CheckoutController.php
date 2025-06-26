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
            $order = DB::transaction(function () use ($cart) {
                $totalPrice = 0;
                foreach ($cart as $details) {
                    $totalPrice += $details['price'] * $details['quantity'];
                }

                $newOrder = Order::create([
                    'dining_table_id' => session('dining_table_id'),
                    'session_id'      => session()->getId(),
                    'customer_name' => session('customer_name'),
                    'table_number' => session('table_number'),
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                ]);

                foreach ($cart as $id => $details) {
                    OrderItem::create([
                        'order_id' => $newOrder->id,
                        'product_id' => $id,
                        'quantity' => $details['quantity'],
                        'price' => $details['price'],
                        'notes' => $details['notes'],
                    ]);
                }
            
                return $newOrder;
            });

            NewOrderReceived::dispatch($order);

            
            $table = DiningTable::find(session('dining_table_id'));
            if ($table) {
                TableStatusUpdated::dispatch($table->fresh()->load(['activeOrder.orderItems.product', 'latestCompletedOrder.orderItems.product']));
            }
            session()->forget('cart');

            return redirect()->route('order.success', ['order' => $order->id]);

        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Gagal membuat pesanan, silakan coba lagi. Error: ' . $e->getMessage());
        }
    }


    public function success(Order $order)
    {
        return view('customer.checkout.success', compact('order'));
    }
}