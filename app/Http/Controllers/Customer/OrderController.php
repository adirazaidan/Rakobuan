<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Call;
class OrderController extends Controller
{
    /**
     * Menampilkan semua pesanan dan panggilan dalam sesi pelanggan saat ini.
     */
    public function status()
    {
        $sessionId = session()->getId();

        $orders = Order::where('session_id', $sessionId)
                       ->with('orderItems.product')
                       ->latest()
                       ->get();

        $calls = Call::where('session_id', $sessionId)
                     ->latest()
                     ->get();

        $activities = $orders->concat($calls)->sortByDesc('created_at');
        return view('customer.order.status', compact('activities'));
    }
}