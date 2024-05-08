<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = $this->getproduct();

        foreach ($product as $key => $value) {
            if ($value->is_varied) {
                $this->variants($value->id);
            } else {
                $this->variant($value->id);
            }
        }
    }

    private function getproduct()
    {
        return DB::table('products')->select(['id', 'is_varied'])->get()->toArray();
    }

    private function variants($product_id)
    {
        for ($i=0; $i < rand(2,5); $i++) { 
            $this->variant($product_id);
        }
    }

    private function variant($product_id)
    {
        $faker = Factory::create();
        $name  = $faker->unique()->colorName();

        DB::table('variants')->insert([
            'product_id'    => $product_id,
            'name'          => $name,
            'min_order'     => rand(1,2),
            'max_order'     => rand(5,10),
            'idr_price'     => $faker->randomFloat(2, 5000, 100000),
            'usd_price'     => $faker->randomFloat(2, 0, 100),
            'stock'         => rand(0, 100),
            'created_at'    => now(),
        ]);
    }
}
