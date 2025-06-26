<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use Illuminate\Http\Request;
use App\Events\TableStatusUpdated;
use App\Events\AvailableTablesUpdated;
class CustomerSessionController extends Controller
{
    /**
     * Menampilkan halaman/form login untuk pelanggan.
     */
    public function create()
    {
        if (session()->has('customer_name')) {
            return redirect()->route('customer.menu.index');
        }

        $availableTables = DiningTable::where('is_locked', false)
                                      ->whereNull('session_id')
                                      ->get()
                                      ->sortBy('name', SORT_NATURAL);

        $tablesByLocation = $availableTables->groupBy('location');

        return view('customer.login', compact('tablesByLocation'));
    }

    /**
     * Memproses form login, memvalidasi, dan menyimpan data ke session.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dining_table_id' => 'required|exists:dining_tables,id',
            'customer_name'   => 'required|string|max:100',
        ]);

        $table = DiningTable::findOrFail($validated['dining_table_id']);

        if ($table->is_locked || !is_null($table->session_id)) {
            return back()->with('error', 'Meja yang Anda pilih tidak tersedia saat ini. Silakan pilih meja lain.');
        }

        $table->update(['session_id' => session()->getId()]);
        TableStatusUpdated::dispatch($table->fresh()->load('activeOrder.orderItems.product', 'latestCompletedOrder.orderItems.product'));
        session([
            'dining_table_id' => $table->id,
            'customer_name'   => $validated['customer_name'],
            'table_number'    => $table->name,
        ]);

        // Redirect ke halaman menu utama
        return redirect()->route('customer.menu.index');
    }

    /**
     * Menghapus sesi pelanggan (logout).
     */
    public function destroy(Request $request)
    {
        if (session()->has('dining_table_id')) {
            $table = DiningTable::find(session('dining_table_id'));
            if ($table) {
                $table->update(['session_id' => null]);
                TableStatusUpdated::dispatch($table->fresh()->load(['activeOrder.orderItems.product', 'latestCompletedOrder.orderItems.product']));
                AvailableTablesUpdated::dispatch();
            }
        }

        $request->session()->flush();
        return redirect()->route('customer.login.form');
    }

    public function getAvailableTables()
    {
        $availableTables = DiningTable::where('is_locked', false)
                                    ->whereNull('session_id')
                                    ->get()
                                    ->sortBy('name', SORT_NATURAL);

        $tablesByLocation = $availableTables->groupBy('location');

        // Return view partial yang hanya berisi <option>
        return view('customer.partials._tables_options', compact('tablesByLocation'));
    }
}