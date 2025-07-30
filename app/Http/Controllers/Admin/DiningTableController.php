<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\Call;
use App\Models\Outlet;
use Illuminate\Http\Request;
use App\Events\TableStatusUpdated; 
use App\Events\SessionCleared;     
use App\Events\AvailableTablesUpdated;
use App\Models\Setting;

class DiningTableController extends Controller
{
    public function index(Request $request)
    {
        $locations = DiningTable::select('location')->whereNotNull('location')->distinct()->pluck('location');
        $selectedLocation = $request->input('location');

         $query = DiningTable::with(['orders.orderItems.product', 'calls']);

        if ($selectedLocation) {
            $query->where('location', $selectedLocation);
        }
        
        $allTables = $query->get();
        $tables = $allTables->sortBy('name', SORT_NATURAL);
        return view('admin.dining-tables.index', compact('tables', 'locations', 'selectedLocation'));
    }

    public function create()
    {
        return view('admin.dining-tables.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:dining_tables,name',
            'notes' => 'nullable|string',
            'location' => 'required|in:Indoor,Outdoor,Outdoor Atas,VIP',
        ]);
        DiningTable::create($validated);
        return redirect()->route('admin.dining-tables.index')->with('success', 'Meja baru berhasil ditambahkan.');
    }

    public function edit(DiningTable $diningTable)
    {
        return view('admin.dining-tables.edit', compact('diningTable'));
    }

    public function update(Request $request, DiningTable $diningTable)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:dining_tables,name,' . $diningTable->id,
            'notes' => 'nullable|string',
            'location' => 'required|in:Indoor,Outdoor,Outdoor Atas,VIP',
            'is_locked' => 'boolean',
        ]);
        $validated['is_locked'] = $request->has('is_locked');
        $diningTable->update($validated);

        TableStatusUpdated::dispatch($diningTable->id);
        AvailableTablesUpdated::dispatch();

        return redirect()->route('admin.dining-tables.index')->with('success', 'Data meja berhasil diperbarui.');
    }

    public function destroy(DiningTable $diningTable)
    {
        $diningTable->delete();
        return redirect()->route('admin.dining-tables.index')->with('success', 'Meja berhasil dihapus.');
    }

    public function lockAll()
    {
        DiningTable::query()->update(['is_locked' => true]);
        Setting::updateOrCreate(['key' => 'accepting_orders'], ['value' => 'false']);
        return redirect()->route('admin.dining-tables.index')->with('success', 'Semua meja dikunci dan pesanan ditutup.');
    }

    public function unlockAll()
    {
        DiningTable::query()->update(['is_locked' => false]);
        Setting::updateOrCreate(['key' => 'accepting_orders'], ['value' => 'true']);
        return redirect()->route('admin.dining-tables.index')->with('success', 'Semua meja dibuka dan pesanan dibuka kembali.');
    }

    public function clearSession(DiningTable $diningTable)
    {
        $sessionIdToClear = $diningTable->session_id;
        if ($sessionIdToClear) {
            Order::where('session_id', $sessionIdToClear)->where('status', 'pending')->update(['status' => 'cancelled']);
            Call::where('session_id', $sessionIdToClear)->where('status', 'pending')->update(['status' => 'cancelled']);
            SessionCleared::dispatch($sessionIdToClear);
        }

        $diningTable->update(['session_id' => null]);
        TableStatusUpdated::dispatch($diningTable->id);
        AvailableTablesUpdated::dispatch();

        return response()->json(['success' => true, 'message' => 'Sesi berhasil dibersihkan.']);
    }



    public function renderCard(DiningTable $diningTable)
    {
        $table = $diningTable->load([
            'orders.orderItems.product', 
            'calls' 
        ]);
        return view('admin.dining-tables._card', compact('table'));
    }
}