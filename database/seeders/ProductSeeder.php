<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private $products = [
        "Swirl"                         => "Abstract painting by Jackson Pollock (Abstract Expressionism)",
        "Girl with a Pearl Earring"     => "Portrait painting by Johannes Vermeer (Baroque)",
        "The Thinker"                   => "Figurative sculpture by Auguste Rodin (Modern)",
        "Teton Range"                   => "Landscape photography by Ansel Adams (Black and White)",
        "Afghan Girl"                   => "Portrait photography by Steve McCurry (Photojournalism)",
        "Night City"                    => "Digital art illustration by Beeple (Crypto Art)",
        "I ❤ NY"                        => "Digital art graphic design by Milton Glaser (Pop Art)",
        "Ukiran Kayu Bali"              => "Traditional Balinese wood carving from Indonesia",
        "Batik Mega Mendung"            => "A motif of traditional batik from Central Java, Indonesia",
        "Tapestry Wall Hanging"         => "A decorative textile weaving art form",
        "Cloud Gate"                    => "A stainless steel sculpture by Anish Kapoor in Chicago",
        "Rumah Fallingwater"            => "A house designed by Frank Lloyd Wright in Pennsylvania, USA",
        "Desain Karakter Video Game"    => "Concept art for a video game character",
        "Sprite Video Game Klasik"      => "Pixel art sprites from a classic video game",
        "Kain Kente"                    => "A traditional handwoven cloth from Ghana",
        "Tiket Teater Drama"            => "A ticket to see a play",
        "Pertunjukan Tari Kontemporer"  => "Tickets to a modern dance performance",
        "Kind of Blue"                  => "A jazz music album by Miles Davis (Modal Jazz)",
        "Forest of Numbers"             => "An interactive art installation by Rafael Lozano-Hemmer (Mexico)",
        "Pameran Seni Multimedia VR"    => "A ticket to a virtual reality art exhibition",
    ];
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->products as $name => $desc) {
            
            DB::table('products')->insert([
                'name'          => $name,
                'slug'          => Str::slug($name),
                'is_varied'     => rand(0,1),
                'created_at'    => now(),
            ]);
        }
    }
}
