<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Sesuai permintaan, dashboard pertama adalah Orderan
        // Untuk sekarang, kita arahkan ke view dashboard
        // Logika orderan akan kita tambahkan nanti
        return view('admin.dashboard');
    }
}