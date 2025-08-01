<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Call;
use Illuminate\Http\Request;
use App\Events\TableStatusUpdated;
use App\Events\CallStatusUpdated;

class CallController extends Controller
{
    // Menampilkan panggilan yang aktif (pending & handled)
    public function index(Request $request)
    {
        $query = Call::query();
        if ($search = $request->input('search')) {
            $query->where('call_number', 'LIKE', '%' . $search . '%');
        }

        $status = $request->input('status');
        if ($status === 'pending' || $status === 'handled') {
            $query->where('status', $status);
        } elseif (is_null($status) || $status === 'active') {
            $query->whereIn('status', ['pending', 'handled']);
        }
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $calls = $query->latest()->paginate(10);
        return view('admin.calls.index', compact('calls'));
    }

    // Menampilkan riwayat panggilan (completed)
    public function history(Request $request)
    {
        $query = Call::query();
        if ($search = $request->input('search')) {
            $query->where('call_number', 'LIKE', '%' . $search . '%');
        }

        $status = $request->input('status');
        if ($status === 'completed') {
            $query->where('status', $status);
        } elseif (is_null($status) || $status === 'history') {
            $query->where('status', 'completed');
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('updated_at', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('updated_at', '<=', $endDate);
        }
        
        $calls = $query->latest()->paginate(10);
        return view('admin.calls.history', compact('calls'));
    }

    // Mengubah status panggilan
    public function updateStatus(Request $request, Call $call)
    {
        $request->validate(['status' => 'required|string|in:handled,completed']);
        $call->update(['status' => $request->status]);

        TableStatusUpdated::dispatch($call->dining_table_id);
        CallStatusUpdated::dispatch($call);
        
        return redirect()->back()->with('success', 'Status panggilan berhasil diperbarui.');
    }

    // Menghapus panggilan
    public function destroy(Call $call, Request $request)
    {
        $call->delete();
        
        return redirect()->back()->with('success', 'Panggilan berhasil dihapus.');
    }

    public function print(Call $call)
    {
        return view('admin.calls.print', compact('call'));
    }
}