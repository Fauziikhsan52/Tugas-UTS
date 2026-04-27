<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Cek jika user sudah ada
        $user = DB::table('users')->where('email', 'fauzi@gmail.com')->first();
        if ($user) {
            $userId = $user->id;
            echo "User Fauzi already exists, using existing user ID: $userId\n";
        } else {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Fauzi',
                'email' => 'fauzi@gmail.com',
                'password' => Hash::make('123456'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Buat categories
        $cat1 = DB::table('categories')->insertGetId([
            'nama' => 'Programming',
            'deskripsi' => 'Buku tentang pemrograman komputer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cat2 = DB::table('categories')->insertGetId([
            'nama' => 'Database',
            'deskripsi' => 'Buku tentang database dan SQL',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cat3 = DB::table('categories')->insertGetId([
            'nama' => 'Web Development',
            'deskripsi' => 'Buku tentang pengembangan web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat books
        $book1 = DB::table('books')->insertGetId([
            'judul' => 'PHP Dasar untuk Pemula',
            'penulis' => 'John Doe',
            'penerbit' => 'Elex Media',
            'tahun' => 2023,
            'stok' => 10,
            'harga' => 75000,
            'cover' => 'https://via.placeholder.com/150',
            'category_id' => $cat1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $book2 = DB::table('books')->insertGetId([
            'judul' => 'Laravel Framework',
            'penulis' => 'Jane Smith',
            'penerbit' => 'Gramedia',
            'tahun' => 2024,
            'stok' => 5,
            'harga' => 120000,
            'cover' => 'https://via.placeholder.com/150',
            'category_id' => $cat1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $book3 = DB::table('books')->insertGetId([
            'judul' => 'MySQL untuk Pemula',
            'penulis' => 'Ahmad Budi',
            'penerbit' => 'Elex Media',
            'tahun' => 2023,
            'stok' => 8,
            'harga' => 65000,
            'cover' => 'https://via.placeholder.com/150',
            'category_id' => $cat2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $book4 = DB::table('books')->insertGetId([
            'judul' => 'PostgreSQL Lanjutan',
            'penulis' => 'Siti Aminah',
            'penerbit' => 'Andi Publisher',
            'tahun' => 2024,
            'stok' => 3,
            'harga' => 95000,
            'cover' => 'https://via.placeholder.com/150',
            'category_id' => $cat2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $book5 = DB::table('books')->insertGetId([
            'judul' => 'React JS Modern',
            'penulis' => 'Budi Santoso',
            'penerbit' => 'Gramedia',
            'tahun' => 2024,
            'stok' => 7,
            'harga' => 150000,
            'cover' => 'https://via.placeholder.com/150',
            'category_id' => $cat3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $book6 = DB::table('books')->insertGetId([
            'judul' => 'Vue.js Crash Course',
            'penulis' => 'Dewi Lestari',
            'penerbit' => 'Elex Media',
            'tahun' => 2023,
            'stok' => 6,
            'harga' => 85000,
            'cover' => 'https://via.placeholder.com/150',
            'category_id' => $cat3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat cart untuk user
        DB::table('carts')->insert([
            'user_id' => $userId,
            'book_id' => $book1,
            'jumlah' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('carts')->insert([
            'user_id' => $userId,
            'book_id' => $book3,
            'jumlah' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat order
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $userId,
            'nomor_order' => 'ORD-' . strtoupper('SAMPLE1'),
            'status' => 'completed',
            'total_harga' => 140000,
            'alamat_pengiriman' => 'Jl. Merdeka No. 10, Jakarta',
            'catatan' => 'Pesanan sample',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat order items
        DB::table('order_items')->insert([
            'order_id' => $orderId,
            'book_id' => $book2,
            'jumlah' => 1,
            'harga_saat_pembelian' => 120000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('order_items')->insert([
            'order_id' => $orderId,
            'book_id' => $book5,
            'jumlah' => 1,
            'harga_saat_pembelian' => 150000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat reviews
        DB::table('reviews')->insert([
            'user_id' => $userId,
            'book_id' => $book1,
            'rating' => 5,
            'komentar' => 'Buku sangat bagus untuk pemula!',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('reviews')->insert([
            'user_id' => $userId,
            'book_id' => $book2,
            'rating' => 4,
            'komentar' => 'Laravel explained well, recommended!',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('reviews')->insert([
            'user_id' => $userId,
            'book_id' => $book5,
            'rating' => 5,
            'komentar' => 'React JS terbaik yang pernah saya baca',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "Sample data created successfully!\n";
    }
}