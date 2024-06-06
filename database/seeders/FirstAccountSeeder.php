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
            'email'             => 'test@email.com',
            'password'          => Hash::make('user#2022!'),
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        //admin token
        DB::table('personal_access_tokens')->insert([
            'id'            => 1,
            'tokenable_type'=> 'App\Models\Admin',
            'tokenable_id'  => 1,
            'name'          => 'admin',
            'token'         => 'ec0074c1b3c3b6a78d8867b0a9d451f0fee1f021b31273e8e2326e0d25c849bd',
            'abilities'     => '["is_admin","admin_master"]',
            'last_used_at'  => null,
            'expires_at'    => now()->addMonths(3),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}
