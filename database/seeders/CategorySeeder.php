<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'description' => 'Produk makanan dan snack'],
            ['name' => 'Minuman', 'description' => 'Produk minuman dan beverages'],
            ['name' => 'Kebersihan', 'description' => 'Produk kebersihan rumah tangga'],
            ['name' => 'Elektronik', 'description' => 'Produk elektronik dan aksesoris'],
            ['name' => 'Lainnya', 'description' => 'Produk lainnya'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }
    }
}
