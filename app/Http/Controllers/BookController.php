<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // 🔹 GET ALL BOOKS
    public function index()
    {
        $books = Book::with('category')->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar buku',
            'data' => $books
        ], 200);
    }

    // 🔹 CREATE BOOK
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // upload gambar
            if ($request->hasFile('cover_image')) {
                $validated['cover_image'] = $request->file('cover_image')
                    ->store('covers', 'public');
            }

            $book = Book::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Buku berhasil ditambahkan',
                'data' => $book
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan buku',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 GET DETAIL
    public function show($id)
    {
        $book = Book::with(['category', 'reviews'])->find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $book
        ], 200);
    }

    // 🔹 UPDATE
    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'author' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'stock' => 'sometimes|integer|min:0',
                'category_id' => 'sometimes|exists:categories,id',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // upload gambar baru
            if ($request->hasFile('cover_image')) {

                // hapus lama
                if ($book->cover_image) {
                    Storage::disk('public')->delete($book->cover_image);
                }

                $validated['cover_image'] = $request->file('cover_image')
                    ->store('covers', 'public');
            }

            $book->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Buku berhasil diperbarui',
                'data' => $book
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update buku',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 DELETE
    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        // hapus gambar
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus'
        ], 200);
    }
}