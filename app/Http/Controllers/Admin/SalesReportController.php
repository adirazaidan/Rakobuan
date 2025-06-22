<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\OrderItem;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $outlets = Outlet::all();
        $selectedOutletId = $request->input('outlet_id');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();

        // --- Query Utama untuk Pesanan ---
        $query = Order::where('status', 'completed')
                      ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        if ($selectedOutletId) {
            $query->whereHas('orderItems.product.category', function ($q) use ($selectedOutletId) {
                $q->where('outlet_id', $selectedOutletId);
            });
        }
        
        $orders = $query->with('orderItems.product')->latest()->get();
        $totalRevenue = $orders->sum('total_price');
        $totalOrders = $orders->count();

        // --- Analisis: Menu Best Seller ---
        $bestSellerQuery = OrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
            $q->where('status', 'completed')->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        });

        if ($selectedOutletId) {
            $bestSellerQuery->whereHas('product.category', function($q) use ($selectedOutletId){
                $q->where('outlet_id', $selectedOutletId);
            });
        }
        
        $bestSellingProducts = $bestSellerQuery
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product') // Eager load detail produk
            ->limit(5) // Ambil 5 teratas
            ->get();


        // --- Analisis: Pelanggan Teratas ---
        // Kita gunakan query dasar yang sama dengan query utama untuk konsistensi filter
        $topCustomersQuery = Order::where('status', 'completed')
                                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        if ($selectedOutletId) {
            $topCustomersQuery->whereHas('orderItems.product.category', function ($q) use ($selectedOutletId) {
                $q->where('outlet_id', $selectedOutletId);
            });
        }

        $topCustomers = $topCustomersQuery
            ->select('customer_name', DB::raw('COUNT(*) as total_orders'))
            ->groupBy('customer_name')
            ->orderByDesc('total_orders')
            ->limit(5) // Ambil 5 teratas
            ->get();

        // Kirim semua data ke view
        return view('admin.sales_report.index', compact(
            'orders', 
            'totalRevenue', 
            'totalOrders', 
            'startDate', 
            'endDate',
            'outlets',
            'selectedOutletId',
            'bestSellingProducts', // <-- KIRIM DATA BEST SELLER
            'topCustomers'         // <-- KIRIM DATA PELANGGAN TERATAS
        ));
    }
}