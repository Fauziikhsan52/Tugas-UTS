<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Book;

class CartController extends Controller
{
    private function checkAuth()
    {
        if (!auth('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        return null;
    }

    public function index()
    {
        if ($auth = $this->checkAuth()) return $auth;

        $carts = Cart::with('book')
            ->where('user_id', auth('sanctum')->id())
            ->get();

        $total = $carts->sum(function ($cart) {
            return $cart->book ? ($cart->book->harga * $cart->jumlah) : 0;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $carts,
                'total_harga' => $total
            ]
        ]);
    }

    public function store(Request $request)
    {
        if ($auth = $this->checkAuth()) return $auth;

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        $book = Book::find($validated['book_id']);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        if ($book->stok < $validated['jumlah']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        $cart = Cart::where('user_id', auth('sanctum')->id())
            ->where('book_id', $validated['book_id'])
            ->first();

        if ($cart) {
            $newJumlah = $cart->jumlah + $validated['jumlah'];

            if ($book->stok < $newJumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi untuk jumlah total'
                ], 400);
            }

            $cart->update(['jumlah' => $newJumlah]);
        } else {
            $cart = Cart::create([
                'user_id' => auth('sanctum')->id(),
                'book_id' => $validated['book_id'],
                'jumlah' => $validated['jumlah']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Buku ditambahkan ke keranjang',
            'data' => $cart
        ]);
    }

    public function update(Request $request, $id)
    {
        if ($auth = $this->checkAuth()) return $auth;

        $cart = Cart::with('book')
            ->where('user_id', auth('sanctum')->id())
            ->where('id', $id)
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1'
        ]);

        if (!$cart->book || $cart->book->stok < $validated['jumlah']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        $cart->update(['jumlah' => $validated['jumlah']]);

        return response()->json([
            'success' => true,
            'message' => 'Jumlah item keranjang diupdate',
            'data' => $cart
        ]);
    }

    public function destroy($id)
    {
        if ($auth = $this->checkAuth()) return $auth;

        $cart = Cart::where('user_id', auth('sanctum')->id())
            ->where('id', $id)
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan'
            ], 404);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item keranjang dihapus'
        ]);
    }

    public function clear()
    {
        if ($auth = $this->checkAuth()) return $auth;

        Cart::where('user_id', auth('sanctum')->id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang dikosongkan'
        ]);
    }
}