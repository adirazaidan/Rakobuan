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
    public function index()
    {
        $calls = Call::whereIn('status', ['pending', 'handled'])
                     ->latest()
                     ->get();
        return view('admin.calls.index', compact('calls'));
    }

    // Menampilkan riwayat panggilan (completed)
    public function history()
    {
        $calls = Call::where('status', 'completed')
                     ->latest()
                     ->get();
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
    public function destroy(Call $call)
    {
        $call->delete();
        return redirect()->back()->with('success', 'Panggilan berhasil dihapus.');
    }

        public function print(Call $call)
    {
        return view('admin.calls.print', compact('call'));
    }
}