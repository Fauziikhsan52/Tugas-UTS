<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample user
        DB::table('users')->insert([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create sample categories
        $categories = [
            ['name' => 'Fiction', 'description' => 'Fiction books', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science', 'description' => 'Science books', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Technology', 'description' => 'Technology books', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'History', 'description' => 'History books', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Art', 'description' => 'Art books', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('categories')->insert($categories);

        // Create sample books
        $books = [
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'description' => 'A classic novel about the American Dream',
                'price' => 150000,
                'stock' => 10,
                'cover_image' => null,
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'description' => 'A dystopian novel',
                'price' => 120000,
                'stock' => 15,
                'cover_image' => null,
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'A Brief History of Time',
                'author' => 'Stephen Hawking',
                'description' => 'Explores the universe',
                'price' => 200000,
                'stock' => 8,
                'cover_image' => null,
                'category_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'description' => 'A handbook of agile software craftsmanship',
                'price' => 250000,
                'stock' => 12,
                'cover_image' => null,
                'category_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Art of War',
                'author' => 'Sun Tzu',
                'description' => 'Ancient Chinese military treatise',
                'price' => 100000,
                'stock' => 20,
                'cover_image' => null,
                'category_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('books')->insert($books);

        // Create sample reviews
        $reviews = [
            [
                'user_id' => 1,
                'book_id' => 1,
                'rating' => 5,
                'comment' => 'Amazing book!',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'book_id' => 2,
                'rating' => 4,
                'comment' => 'Very insightful',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'book_id' => 4,
                'rating' => 5,
                'comment' => 'Must read for developers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('reviews')->insert($reviews);
    }
}