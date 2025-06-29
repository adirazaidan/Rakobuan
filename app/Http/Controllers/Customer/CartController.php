<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;


class CartController extends Controller
{
    /**
     * Menambahkan item baru ke keranjang di session.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::with('activeDiscount')->findOrFail($request->product_id);
        $cart = session()->get('cart', []);

        $priceToUse = $product->activeDiscount 
                        ? ($product->price - ($product->price * $product->activeDiscount->percentage / 100)) 
                        : $product->price;

        $productId = $product->id;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += (int)$request->quantity;
        } else {
            $cart[$productId] = [
                "product_id" => $productId,
                "name" => $product->name,
                "quantity" => (int)$request->quantity,
                "price" => $priceToUse,
                "image" => $product->image,
                "notes" => $request->notes
            ];
        }

        session()->put('cart', $cart);
        $grandTotal = 0;
        foreach ($cart as $details) {
            $grandTotal += $details['price'] * $details['quantity'];
        }

        return response()->json([
            'success'   => true,
            'message' => $product->name . ' berhasil ditambahkan ke keranjang!',
            'cartCount' => count($cart),
            'grandTotal' => $grandTotal,
        ]);
    }

    /**
     * Menampilkan halaman keranjang belanja.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $totalPrice = 0;
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($cart as $id => &$details) { 
            if (isset($products[$id])) {
                $details['product'] = $products[$id]; 
                $totalPrice += $details['price'] * $details['quantity'];
            } else {
                unset($cart[$id]);
            }
        }
        
        session()->put('cart', $cart);

        return view('customer.cart.index', compact('cart', 'totalPrice'));
    }

    /**
     * Memperbarui item di dalam keranjang (jumlah & catatan).
     */
    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = (int)$request->quantity;
            $cart[$productId]['notes'] = $request->notes;
            session()->put('cart', $cart);

            $grandTotal = 0;
            foreach ($cart as $details) {
                $grandTotal += $details['price'] * $details['quantity'];
            }

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diperbarui.',
                'grandTotal' => $grandTotal,
                'cartCount' => count($cart)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function remove(Request $request, $productId) // <-- Tambahkan Request $request
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);

            if ($request->wantsJson()) {
                    $grandTotal = 0;
                    foreach ($cart as $details) {
                        $grandTotal += $details['price'] * $details['quantity'];
                    }
                    
                    return response()->json([
                        'success'   => true,
                        'message'   => 'Item berhasil dihapus.',
                        'cartCount' => count($cart),
                        'grandTotal' => $grandTotal
                    ]);
                }

            return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus dari keranjang.');
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);
        }

        return redirect()->route('cart.index')->with('error', 'Item tidak ditemukan di keranjang.');
    }
}