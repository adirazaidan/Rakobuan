<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Call;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mengambil jumlah pesanan dan panggilan yang sedang pending.
     */
    public function getCounts()
    {
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $pendingCallsCount = Call::where('status', 'pending')->count();

        return response()->json([
            'pending_orders' => $pendingOrdersCount,
            'pending_calls' => $pendingCallsCount,
        ]);
    }
}