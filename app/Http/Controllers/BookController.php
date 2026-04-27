<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all();
        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun' => 'nullable|integer|min:1900|max:2100',
            'stok' => 'nullable|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'cover' => 'nullable|string'
        ]);

        $book = Book::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan',
            'data' => $book
        ], 201);
    }

    public function show($id)
    {
        $book = Book::find($id);
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $book
        ]);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'judul' => 'sometimes|string|max:255',
            'penulis' => 'sometimes|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun' => 'nullable|integer|min:1900|max:2100',
            'stok' => 'nullable|integer|min:0',
            'harga' => 'sometimes|numeric|min:0',
            'cover' => 'nullable|string'
        ]);

        $book->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil diupdate',
            'data' => $book
        ]);
    }

    public function destroy($id)
    {
        $book = Book::find($id);
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus'
        ]);
    }
}
