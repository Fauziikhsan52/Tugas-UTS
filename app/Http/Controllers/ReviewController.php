<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        if (!auth('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $bookId = $request->query('book_id');

        if ($bookId) {
            $reviews = Review::with('user')
                ->where('book_id', $bookId)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $reviews = Review::with(['user', 'book'])
                ->where('user_id', auth('sanctum')->id())
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    public function store(Request $request)
    {
        if (!auth('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|between:1,5',
            'komentar' => 'nullable|string'
        ]);

        $existingReview = Review::where('user_id', auth('sanctum')->id())
            ->where('book_id', $validated['book_id'])
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah pernah memberikan review untuk buku ini'
            ], 400);
        }

        $review = Review::create([
            'user_id' => auth('sanctum')->id(),
            'book_id' => $validated['book_id'],
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil ditambahkan',
            'data' => $review->load('user')
        ], 201);
    }

    public function show($id)
    {
        $review = Review::with(['user', 'book'])->find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $review
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!auth('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $review = Review::where('user_id', auth('sanctum')->id())
            ->where('id', $id)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|integer|between:1,5',
            'komentar' => 'nullable|string'
        ]);

        $review->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil diupdate',
            'data' => $review
        ]);
    }

    public function destroy($id)
    {
        if (!auth('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $review = Review::where('user_id', auth('sanctum')->id())
            ->where('id', $id)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review tidak ditemukan'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil dihapus'
        ]);
    }
}