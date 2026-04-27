<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
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

        $orders = Order::with(['items.book', 'user'])
            ->where('user_id', auth('sanctum')->id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        if ($auth = $this->checkAuth()) return $auth;

        $validated = $request->validate([
            'alamat_pengiriman' => 'required|string',
            'catatan' => 'nullable|string'
        ]);

        $user = auth('sanctum')->user();

        $carts = Cart::with('book')
            ->where('user_id', $user->id)
            ->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja kosong'
            ], 400);
        }

        // cek stok
        foreach ($carts as $cart) {
            if (!$cart->book || $cart->book->stok < $cart->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok buku tidak mencukupi"
                ], 400);
            }
        }

        try {
            return DB::transaction(function () use ($user, $carts, $validated) {

                $totalHarga = $carts->sum(function ($cart) {
                    return $cart->book->harga * $cart->jumlah;
                });

                $order = Order::create([
                    'user_id' => $user->id,
                    'nomor_order' => 'ORD-' . strtoupper(Str::random(10)),
                    'status' => 'pending',
                    'total_harga' => $totalHarga,
                    'alamat_pengiriman' => $validated['alamat_pengiriman'],
                    'catatan' => $validated['catatan'] ?? null
                ]);

                foreach ($carts as $cart) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'book_id' => $cart->book_id,
                        'jumlah' => $cart->jumlah,
                        'harga_saat_pembelian' => $cart->book->harga
                    ]);

                    // kurangi stok
                    $cart->book->decrement('stok', $cart->jumlah);
                }

                // hapus cart
                Cart::where('user_id', $user->id)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat',
                    'data' => $order->load(['items.book', 'user'])
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        if ($auth = $this->checkAuth()) return $auth;

        $order = Order::with(['items.book', 'user'])
            ->where('user_id', auth('sanctum')->id())
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function update(Request $request, $id)
    {
        if ($auth = $this->checkAuth()) return $auth;

        $order = Order::where('user_id', auth('sanctum')->id())
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat diubah'
            ], 400);
        }

        $validated = $request->validate([
            'alamat_pengiriman' => 'sometimes|string',
            'catatan' => 'nullable|string'
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil diupdate',
            'data' => $order
        ]);
    }

    public function destroy($id)
    {
        if ($auth = $this->checkAuth()) return $auth;

        $order = Order::with('items.book')
            ->where('user_id', auth('sanctum')->id())
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat dibatalkan'
            ], 400);
        }

        foreach ($order->items as $item) {
            if ($item->book) {
                $item->book->increment('stok', $item->jumlah);
            }
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan'
        ]);
    }
}