<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with('book', 'user')->get();
        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create([
            'user_id' => $request->user()->id,
            'book_id' => $validated['book_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil ditambahkan',
            'data' => $review->load('book', 'user')
        ], 201);
    }

    public function show($id)
    {
        $review = Review::with('book', 'user')->find($id);
        
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $review
        ]);
    }

    public function update(Request $request, $id)
    {
        $review = Review::where('id', $id)->where('user_id', $request->user()->id)->first();
        
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil diperbarui',
            'data' => $review->load('book', 'user')
        ]);
    }

    public function destroy($id)
    {
        $review = Review::where('id', $id)->where('user_id', request()->user()->id)->first();
        
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan tidak ditemukan'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dihapus'
        ]);
    }
}