<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use Illuminate\Http\Request;
use App\Events\TableStatusUpdated;
use App\Events\AvailableTablesUpdated;
class CustomerSessionController extends Controller
{
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

        session()->regenerate(true);
        $table->update(['session_id' => session()->getId()]);
        
        TableStatusUpdated::dispatch($table->id);
        AvailableTablesUpdated::dispatch();

        session([
            'dining_table_id' => $table->id,
            'customer_name'   => $validated['customer_name'],
            'table_number'    => $table->name,
            'login_timestamp' => now()->timestamp,
        ]);
        
        return redirect()->route('customer.menu.index');
    }

    public function destroy(Request $request)
    {
        $tableId = session('dining_table_id');
        if ($tableId) {
            $table = DiningTable::find($tableId);
            if ($table) {
                $table->update(['session_id' => null]);
                TableStatusUpdated::dispatch($tableId);
                AvailableTablesUpdated::dispatch();
            }
        }
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('customer.login.form');
    }

    public function getAvailableTables()
    {
        $availableTables = DiningTable::where('is_locked', false)
                                      ->whereNull('session_id')
                                      ->orderBy('name', 'asc')
                                      ->get();
        
        $tablesByLocation = $availableTables->groupBy('location');
        return view('customer.partials._table_options', compact('tablesByLocation'));
    }
}