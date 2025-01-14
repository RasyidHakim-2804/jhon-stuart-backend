<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        (new AdminSeeder)->run();
        (new UserSeeder)->run();
        (new LanguageSeeder)->run();

        //product
        (new ProductCategorySeeder)->run();
        // (new ProductSeeder)->run();
        // (new ProductVariantSeeder)->run();
        // (new PivotProductCategorySeeder)->run();
        // (new ProductTranslationsSeeder)->run();
        // (new ProductCategoryTranslationSeeder)->run();

        //article
        (new ArticleCategorySeeder)->run();
        (new ArticleSeeder)->run();
        (new ArticleImagesSeeder)->run();
    }
}
