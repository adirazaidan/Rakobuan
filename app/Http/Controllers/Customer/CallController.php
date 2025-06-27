<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Call;
use Illuminate\Http\Request;
use App\Models\DiningTable;
use App\Events\NewCallReceived;
use App\Events\TableStatusUpdated;

class CallController extends Controller
{
    /**
     * Menyimpan panggilan baru dari pelanggan ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $diningTableId = session('dining_table_id');
        if (!$diningTableId) {
            return response()->json(['error' => 'Sesi meja tidak ditemukan.'], 401);
        }
        
        $call = Call::create([
            'dining_table_id' => $diningTableId,
            'customer_name'   => session('customer_name'),
            'table_number'    => session('table_number'),
            'notes'           => $request->notes,
            'status'          => 'pending',
        ]);

    
        NewCallReceived::dispatch($call);

        TableStatusUpdated::dispatch($diningTableId);

        return response()->json(['message' => 'Panggilan telah terkirim! Pelayan akan segera datang.']);
    }
}