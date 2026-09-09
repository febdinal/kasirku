<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $makanan = Category::where('name', 'Makanan')->first();
        $minuman = Category::where('name', 'Minuman')->first();
        $kebersihan = Category::where('name', 'Kebersihan')->first();

        $products = [
            ['category_id' => $makanan?->id, 'name' => 'Indomie Goreng', 'sku' => 'MKN-001', 'price' => 3500, 'cost_price' => 2800, 'stock' => 100, 'unit' => 'pcs'],
            ['category_id' => $makanan?->id, 'name' => 'Roti Tawar Sari Roti', 'sku' => 'MKN-002', 'price' => 18000, 'cost_price' => 14000, 'stock' => 50, 'unit' => 'pcs'],
            ['category_id' => $makanan?->id, 'name' => 'Chitato Sapi Panggang', 'sku' => 'MKN-003', 'price' => 12000, 'cost_price' => 9500, 'stock' => 75, 'unit' => 'pcs'],
            ['category_id' => $makanan?->id, 'name' => 'Oreo Original', 'sku' => 'MKN-004', 'price' => 8500, 'cost_price' => 6800, 'stock' => 60, 'unit' => 'pcs'],
            ['category_id' => $minuman?->id, 'name' => 'Aqua 600ml', 'sku' => 'MNM-001', 'price' => 5000, 'cost_price' => 3500, 'stock' => 200, 'unit' => 'botol'],
            ['category_id' => $minuman?->id, 'name' => 'Teh Botol Sosro 450ml', 'sku' => 'MNM-002', 'price' => 6500, 'cost_price' => 5000, 'stock' => 150, 'unit' => 'botol'],
            ['category_id' => $minuman?->id, 'name' => 'Coca Cola 330ml', 'sku' => 'MNM-003', 'price' => 8000, 'cost_price' => 6200, 'stock' => 100, 'unit' => 'kaleng'],
            ['category_id' => $minuman?->id, 'name' => 'Kopi Kapal Api Sachet', 'sku' => 'MNM-004', 'price' => 2500, 'cost_price' => 1800, 'stock' => 200, 'unit' => 'sachet'],
            ['category_id' => $kebersihan?->id, 'name' => 'Sabun Lifebuoy 110g', 'sku' => 'KBR-001', 'price' => 7500, 'cost_price' => 5800, 'stock' => 80, 'unit' => 'pcs'],
            ['category_id' => $kebersihan?->id, 'name' => 'Shampoo Pantene 170ml', 'sku' => 'KBR-002', 'price' => 25000, 'cost_price' => 20000, 'stock' => 40, 'unit' => 'botol'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
