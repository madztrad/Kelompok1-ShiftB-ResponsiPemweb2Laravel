<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Dompet & Kartu', 'HP & Elektronik', 'Tas', 'Kunci', 'Dokumen', 'Pakaian & Aksesoris', 'Lainnya'] as $name) {
            Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
