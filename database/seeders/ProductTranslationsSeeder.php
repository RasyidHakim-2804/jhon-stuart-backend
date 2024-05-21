<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTranslationsSeeder extends Seeder
{
    private $artworks = [
        [
            'id' => "Lukisan abstrak oleh Jackson Pollock (Ekspresionisme Abstrak)",
            'en' => "Abstract painting by Jackson Pollock (Abstract Expressionism)"
        ],
        [
            'id' => "Lukisan potret oleh Johannes Vermeer (Barok)",
            'en' => "Portrait painting by Johannes Vermeer (Baroque)"
        ],
        [
            'id' => "Patung figuratif oleh Auguste Rodin (Modern)",
            'en' => "Figurative sculpture by Auguste Rodin (Modern)"
        ],
        [
            'id' => "Fotografi pemandangan oleh Ansel Adams (Hitam dan Putih)",
            'en' => "Landscape photography by Ansel Adams (Black and White)"
        ],
        [
            'id' => "Fotografi potret oleh Steve McCurry (Jurnalisme Foto)",
            'en' => "Portrait photography by Steve McCurry (Photojournalism)"
        ],
        [
            'id' => "Ilustrasi seni digital oleh Beeple (Seni Kripto)",
            'en' => "Digital art illustration by Beeple (Crypto Art)"
        ],
        [
            'id' => "Desain grafis seni digital oleh Milton Glaser (Seni Pop)",
            'en' => "Digital art graphic design by Milton Glaser (Pop Art)"
        ],
        [
            'id' => "Ukiran kayu tradisional Bali dari Indonesia",
            'en' => "Traditional Balinese wood carving from Indonesia"
        ],
        [
            'id' => "Motif batik tradisional dari Jawa Tengah, Indonesia",
            'en' => "A motif of traditional batik from Central Java, Indonesia"
        ],
        [
            'id' => "Seni tenun tekstil dekoratif",
            'en' => "A decorative textile weaving art form"
        ],
        [
            'id' => "Patung baja tahan karat oleh Anish Kapoor di Chicago",
            'en' => "A stainless steel sculpture by Anish Kapoor in Chicago"
        ],
        [
            'id' => "Rumah yang dirancang oleh Frank Lloyd Wright di Pennsylvania, USA",
            'en' => "A house designed by Frank Lloyd Wright in Pennsylvania, USA"
        ],
        [
            'id' => "Seni konsep untuk karakter video game",
            'en' => "Concept art for a video game character"
        ],
        [
            'id' => "Sprite seni piksel dari video game klasik",
            'en' => "Pixel art sprites from a classic video game"
        ],
        [
            'id' => "Kain tenun tradisional dari Ghana",
            'en' => "A traditional handwoven cloth from Ghana"
        ],
        [
            'id' => "Tiket untuk menonton drama",
            'en' => "A ticket to see a play"
        ],
        [
            'id' => "Tiket untuk pertunjukan tari kontemporer",
            'en' => "Tickets to a modern dance performance"
        ],
        [
            'id' => "Album musik jazz oleh Miles Davis (Jazz Modal)",
            'en' => "A jazz music album by Miles Davis (Modal Jazz)"
        ],
        [
            'id' => "Instalasi seni interaktif oleh Rafael Lozano-Hemmer (Meksiko)",
            'en' => "An interactive art installation by Rafael Lozano-Hemmer (Mexico)"
        ],
        [
            'id' => "Tiket untuk pameran seni realitas virtual",
            'en' => "A ticket to a virtual reality art exhibition"
        ],
    ];
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = $this->getproduct();

        $count = 0;
        foreach ($product as $key => $value) {

            // indonesia language
            DB::table('product_translations')->insert([
                'product_id'        => $value->id,
                'lang'              => 'id',
                'description'       => $this->artworks[$count]['id'],
                'full_description'  => $this->artworks[$count]['id'],
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // english language
            DB::table('product_translations')->insert([
                'product_id'        => $value->id,
                'lang'              => 'en',
                'description'       => $this->artworks[$count]['en'],
                'full_description'  => $this->artworks[$count]['en'],
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
            $count++;
        }
    }

    private function getproduct()
    {
        return DB::table('products')->select(['id'])->orderBy('id')->get()->toArray();
    }
}
