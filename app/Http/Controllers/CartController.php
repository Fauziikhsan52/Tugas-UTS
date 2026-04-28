<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Book;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::where('user_id', $request->user()->id)->with('book')->get();
        return response()->json([
            'success' => true,
            'data' => $carts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $book = Book::find($validated['book_id']);
        
        if ($book->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        $cartItem = Cart::where('user_id', $request->user()->id)
            ->where('book_id', $validated['book_id'])
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $validated['quantity'];
            
            if ($book->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi'
                ], 400);
            }

            $cartItem->update(['quantity' => $newQuantity]);
            
            return response()->json([
                'success' => true,
                'message' => 'Jumlah buku diperbarui di keranjang',
                'data' => $cartItem->load('book')
            ]);
        }

        $cart = Cart::create([
            'user_id' => $request->user()->id,
            'book_id' => $validated['book_id'],
            'quantity' => $validated['quantity'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Buku ditambahkan ke keranjang',
            'data' => $cart->load('book')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::where('id', $id)->where('user_id', $request->user()->id)->first();
        
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $book = Book::find($cart->book_id);
        
        if ($book->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        $cart->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'success' => true,
            'message' => 'Jumlah buku diperbarui',
            'data' => $cart->load('book')
        ]);
    }

    public function destroy($id)
    {
        $cart = Cart::where('id', $id)->where('user_id', request()->user()->id)->first();
        
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan'
            ], 404);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item dihapus dari keranjang'
        ]);
    }

    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang dikosongkan'
        ]);
    }
}