<?php

namespace App\View\Composers;

use App\Models\Outlet;
use Illuminate\View\View;

class OutletComposer
{
    /**
     * Mengikat data ke view.
     * Method ini akan dipanggil setiap kali layout 'layouts.customer' dirender.
     */
    public function compose(View $view): void
    {
        // Ambil semua outlet dari database, lalu kirimkan ke view
        // dengan nama variabel 'outletsForSidebar'.
        $view->with('outletsForSidebar', Outlet::all());
    }
}