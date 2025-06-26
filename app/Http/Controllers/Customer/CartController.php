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

        $product = Product::with('activeDiscount')->findOrFail($request->product_id); // <-- MUAT RELASI DISKON
        $cart = session()->get('cart', []);

        // Tentukan harga yang akan digunakan (harga diskon jika ada, jika tidak, harga normal)
        $priceToUse = $product->activeDiscount 
                        ? ($product->price - ($product->price * $product->activeDiscount->percentage / 100)) 
                        : $product->price;

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += (int)$request->quantity;
        } else {
            $cart[$product->id] = [
                "name" => $product->name,
                "quantity" => (int)$request->quantity,
                "price" => $priceToUse, // <-- GUNAKAN HARGA YANG SUDAH DITENTUKAN
                "image" => $product->image,
                "notes" => $request->notes
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'message' => $product->name . ' berhasil ditambahkan ke keranjang!',
            'cartCount' => count($cart)
        ]);
    }

    /**
     * Menampilkan halaman keranjang belanja.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $totalPrice = 0;

        // Hitung total harga dari semua item di keranjang
        foreach ($cart as $details) {
            $totalPrice += $details['price'] * $details['quantity'];
        }

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
            // Update data di session
            $cart[$productId]['quantity'] = (int)$request->quantity;
            $cart[$productId]['notes'] = $request->notes;
            session()->put('cart', $cart);

            // Hitung ulang total harga keseluruhan untuk dikirim kembali
            $grandTotal = 0;
            foreach ($cart as $details) {
                $grandTotal += $details['price'] * $details['quantity'];
            }

            // Kembalikan respon JSON
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
    public function remove($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]); // Hapus item dari array
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus dari keranjang.');
        }

        return redirect()->route('cart.index')->with('error', 'Item tidak ditemukan di keranjang.');
    }
}