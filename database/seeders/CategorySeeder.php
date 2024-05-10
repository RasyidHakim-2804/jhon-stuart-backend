<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    protected $art_categories = [
        "Abstract painting",
        "Portrait painting",
        "Figurative sculpture",
        "Landscape photography",
        "Portrait photography",
        "Digital art illustration",
        "Digital art graphic design",
        "Handicrafts",
        "Traditional batik",
        "Modern weaving",
        "Installation sculpture",
        "Architecture photography",
        "Concept art",
        "Pixel art",
        "Textile weaving",
        "Drama theater",
        "Contemporary dance",
        "Jazz music",
        "Interactive art",
        "VR multimedia art"
    ];
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->art_categories as $category) {
            DB::table('categories')->insert([
                'name'       => $category,
                'slug'       => Str::slug($category),
                'created_at' => now(),
            ]);
        }
    }
}
