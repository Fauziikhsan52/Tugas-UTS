<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems.book', 'user')->get();
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string',
        ]);

        $user = $request->user();
        $carts = Cart::where('user_id', $user->id)->with('book')->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja kosong'
            ], 400);
        }

        $totalAmount = 0;
        foreach ($carts as $cart) {
            $totalAmount += $cart->book->price * $cart->quantity;
        }

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'shipping_address' => $validated['shipping_address'],
        ]);

        foreach ($carts as $cart) {
            OrderItem::create([
                'order_id' => $order->id,
                'book_id' => $cart->book_id,
                'quantity' => $cart->quantity,
                'price' => $cart->book->price,
            ]);
        }

        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat',
            'data' => $order->load('orderItems.book')
        ], 201);
    }

    public function show($id)
    {
        $order = Order::with('orderItems.book', 'user')->find($id);
        
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
        $order = Order::find($id);
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,processing,shipped,delivered,cancelled',
            'shipping_address' => 'sometimes|string',
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil diperbarui',
            'data' => $order
        ]);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        OrderItem::where('order_id', $id)->delete();
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dihapus'
        ]);
    }
}