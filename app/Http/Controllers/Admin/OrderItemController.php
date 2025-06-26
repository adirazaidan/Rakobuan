<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Events\TableStatusUpdated; // Import event kita
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    /**
     * Menandai satu kuantitas item sebagai telah diantar.
     */
    public function deliver(OrderItem $orderItem)
    {
        if ($orderItem->quantity_delivered < $orderItem->quantity) {
            $orderItem->increment('quantity_delivered');
        }

        $order = $orderItem->order->load('orderItems'); // Muat ulang order dengan item-itemnya

        $allItemsDelivered = $order->orderItems->every(function ($item) {
            return $item->quantity <= $item->quantity_delivered;
        });

        if ($allItemsDelivered) {
            // Jika ya, ubah status order utama menjadi 'completed'
            $order->update(['status' => 'completed']);
            // Baris di bawah ini kita hapus/komentari agar sesi tidak otomatis bersih
            // $order->diningTable()->update(['session_id' => null]);
        }

        // Muat relasi diningTable dengan data order terbarunya untuk dikirim
        TableStatusUpdated::dispatch($order->dining_table_id);

        // Kembalikan respon JSON, bukan redirect
        return response()->json(['success' => true, 'message' => 'Status pengantaran diperbarui.']);
    }
}