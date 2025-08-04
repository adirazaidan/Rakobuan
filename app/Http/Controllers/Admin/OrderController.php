<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Events\OrderStatusUpdated;

class OrderController extends Controller
{
    // Menampilkan pesanan yang sedang berjalan (pending & processing)
    public function index(Request $request)
    {
        $query = Order::with('orderItems.product');

        if ($search = $request->input('search')) {
            $query->where('order_number', 'LIKE', '%' . $search . '%');
        }
        $status = $request->input('status', 'current');
        if ($status === 'pending' || $status === 'processing' || $status === 'completed' || $status === 'cancelled') {
            $query->where('status', $status);
        } elseif ($status === 'current') {
            $query->whereIn('status', ['pending', 'processing']);
        }
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orders = $query->latest()->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    // Mengubah status pesanan (Tangani/Selesai)
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:processing,completed,cancelled']);

        $order->update(['status' => $request->status]);

        OrderStatusUpdated::dispatch($order);

        return redirect()->route('admin.orders.index', $request->query())->with('success', 'Status pesanan berhasil diperbarui.');
    }

    // Menghapus pesanan
    public function destroy(Order $order, Request $request)
    {
        $order->delete();

        // Check if the request came from the history page
        $referer = $request->header('referer');
        if ($referer && str_contains($referer, '/orders/history')) {
            return redirect()->route('admin.orders.history', $request->query())->with('success', 'Pesanan berhasil dihapus.');
        }

        return redirect()->route('admin.orders.index', $request->query())->with('success', 'Pesanan berhasil dihapus.');
    }

    public function history(Request $request)
    {
        $query = Order::with('orderItems.product');

        if ($search = $request->input('search')) {
            $query->where('order_number', 'LIKE', '%' . $search . '%');
        }

        $status = $request->input('status');
        if ($status === 'completed' || $status === 'cancelled') {
            $query->where('status', $status);
        } elseif ($status === 'all' || is_null($status)) {
            $query->whereIn('status', ['completed', 'cancelled']);
        }
        
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('updated_at', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('updated_at', '<=', $endDate);
        }
        $orders = $query->latest()->paginate(10);

        return view('admin.orders.history', compact('orders'));
    }

    public function print(Order $order)
    {
        return view('admin.orders.print', compact('order'));
    }
}
