<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Menampilkan pesanan yang sedang berjalan (pending & processing)
    public function index()
    {
        $orders = Order::whereIn('status', ['pending', 'processing'])
                        ->with('orderItems.product') // Eager load untuk efisiensi
                        ->latest() // Tampilkan yang terbaru di atas
                        ->get();

        return view('admin.orders.index', compact('orders'));
    }

    // Mengubah status pesanan (Tangani/Selesai)
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:processing,completed']);

        $order->update(['status' => $request->status]);

        return redirect()->route('admin.orders.index')->with('success', 'Status pesanan berhasil diperbarui.');
    }

    // Menghapus pesanan
    public function destroy(Order $order)
    {
        // Karena ada onDelete('cascade') di migrasi order_items,
        // semua item terkait akan ikut terhapus.
        $order->delete();

        return redirect()->back()->with('success', 'Pesanan berhasil dihapus.');
    }

    public function history()
    {
        // Logikanya sama dengan index(), hanya beda filter status
        $orders = Order::where('status', 'completed')
                        ->with('orderItems.product')
                        ->latest()
                        ->get();

        // Kita akan membuat view baru bernama 'history.blade.php'
        return view('admin.orders.history', compact('orders'));
    }
}