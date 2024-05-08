<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FirstAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            'id'                => 1,
            'uuid'              => Str::ulid()->toBase58(), 
            'first_name'        => 'Master',
            'last_name'         => 'Admin',
            'email'             => 'info@admin.com',
            'password'          => Hash::make('admin#2022!'),
            'active'            => 1,
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        DB::table('users')->insert([
            'id'                => 1, 
            'uuid'              => Str::ulid()->toBase58(),
            'first_name'        => 'Master',
            'last_name'         => 'User',
            'email'             => 'test@emai.com',
            'password'          => Hash::make('user#2022!'),
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }
}
