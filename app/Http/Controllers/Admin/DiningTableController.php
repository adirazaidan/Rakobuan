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
    /**
     * Menampilkan daftar meja dengan filter lokasi dan pengurutan khusus.
     * Meja yang sedang diduduki akan tampil di bagian atas.
     */
    public function index(Request $request)
    {
        // Ambil semua lokasi unik dari database untuk filter dropdown
        $locations = DiningTable::select('location')->whereNotNull('location')->distinct()->pluck('location')->sort();
        $selectedLocation = $request->input('location');

        // Memuat relasi yang diperlukan secara eager loading
        $query = DiningTable::with(['orders.orderItems.product', 'calls']);

        // Menerapkan filter lokasi jika ada
        if ($selectedLocation) {
            $query->where('location', $selectedLocation);
        }
        
        $allTables = $query->get();

        // Mengurutkan koleksi: diduduki di atas, lalu diurutkan berdasarkan nama
        $tables = $allTables->sort(function ($a, $b) {
            // Logika untuk menentukan status 'diduduki'
            $aOccupied = $a->session_id || $a->activeOrders->isNotEmpty();
            $bOccupied = $b->session_id || $b->activeOrders->isNotEmpty();

            // Aturan pengurutan utama: yang diduduki (-1) lebih dulu dari yang tidak diduduki (1)
            if ($aOccupied !== $bOccupied) {
                return $aOccupied ? -1 : 1;
            }

            // Aturan pengurutan kedua: berdasarkan nama meja secara natural (alfanumerik)
            return strnatcmp($a->name, $b->name);
        });

        return view('admin.dining-tables.index', compact('tables', 'locations', 'selectedLocation'));
    }

    /**
     * Menampilkan form untuk membuat meja baru.
     */
    public function create()
    {
        return view('admin.dining-tables.create');
    }

    /**
     * Menyimpan meja baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:dining_tables,name',
            'notes' => 'nullable|string',
            'location' => 'required|in:Indoor,Outdoor,Outdoor Atas,VIP,Takeaway',
        ]);
        DiningTable::create($validated);
        return redirect()->route('admin.dining-tables.index')->with('success', 'Meja baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit meja.
     */
    public function edit(DiningTable $diningTable)
    {
        return view('admin.dining-tables.edit', compact('diningTable'));
    }

    /**
     * Memperbarui data meja di database.
     */
    public function update(Request $request, DiningTable $diningTable)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:dining_tables,name,' . $diningTable->id,
            'notes' => 'nullable|string',
            'location' => 'required|in:Indoor,Outdoor,Outdoor Atas,VIP,Takeaway',
            'is_locked' => 'boolean',
        ]);
        $validated['is_locked'] = $request->has('is_locked');
        $diningTable->update($validated);

        TableStatusUpdated::dispatch($diningTable->id);
        AvailableTablesUpdated::dispatch();

        return redirect()->route('admin.dining-tables.index')->with('success', 'Data meja berhasil diperbarui.');
    }

    /**
     * Menghapus meja dari database.
     */
    public function destroy(DiningTable $diningTable)
    {
        $diningTable->delete();
        return redirect()->route('admin.dining-tables.index')->with('success', 'Meja berhasil dihapus.');
    }

    /**
     * Mengunci semua meja dan menutup penerimaan pesanan.
     */
    public function lockAll()
    {
        DiningTable::query()->update(['is_locked' => true]);
        Setting::updateOrCreate(['key' => 'accepting_orders'], ['value' => 'false']);
        return redirect()->route('admin.dining-tables.index')->with('success', 'Semua meja dikunci dan pesanan ditutup.');
    }

    /**
     * Membuka kunci semua meja dan membuka penerimaan pesanan.
     */
    public function unlockAll()
    {
        DiningTable::query()->update(['is_locked' => false]);
        Setting::updateOrCreate(['key' => 'accepting_orders'], ['value' => 'true']);
        return redirect()->route('admin.dining-tables.index')->with('success', 'Semua meja dibuka dan pesanan dibuka kembali.');
    }

    /**
     * Membersihkan sesi yang terkait dengan sebuah meja.
     */
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

    /**
     * Mengambil dan merender kartu meja tunggal.
     */
    public function renderCard(DiningTable $diningTable)
    {
        $table = $diningTable->load([
            'orders.orderItems.product', 
            'calls' 
        ]);
        return view('admin.dining-tables._card', compact('table'));
    }
}
