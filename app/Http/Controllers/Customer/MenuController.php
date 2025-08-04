<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Outlet;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menampilkan halaman daftar menu untuk pelanggan.
     */
    public function index(Outlet $outlet = null)
    {
        if (is_null($outlet)) {
            $firstOutlet = Outlet::first();
            if (!$firstOutlet) {
                abort(404, 'Tidak ada outlet yang terdaftar.');
            }
            return redirect()->route('customer.menu.index', $firstOutlet);
        }

        $categories = Category::where('outlet_id', $outlet->id)->get();
        $products = Product::whereHas('category', function ($query) use ($outlet) {
            $query->where('outlet_id', $outlet->id);
        })
        ->with('activeDiscount')
        ->orderByRaw('
            CASE 
                WHEN is_bestseller = 1 AND EXISTS (
                    SELECT 1 FROM discounts 
                    WHERE discounts.product_id = products.id 
                    AND discounts.is_active = 1
                ) THEN 1
                WHEN is_bestseller = 1 THEN 2
                WHEN EXISTS (
                    SELECT 1 FROM discounts 
                    WHERE discounts.product_id = products.id 
                    AND discounts.is_active = 1
                ) THEN 3
                ELSE 4
            END
        ')
        ->orderBy('name')
        ->get();
        
        $cart = session()->get('cart', []);
        $cartItemCount = count($cart);
        $cartTotalPrice = 0;
        foreach ($cart as $details) {
            if (isset($details['price']) && isset($details['quantity'])) {
                $cartTotalPrice += $details['price'] * $details['quantity'];
            }
        }

        $currentOutlet = $outlet;
        
        return view('customer.menu.index', compact('categories', 'products', 'currentOutlet', 'cart', 'cartItemCount', 'cartTotalPrice'));
    }
}