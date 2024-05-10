<?php

namespace Tests\Feature\Api\V1\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_store_categories(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
                        ->json('post', 'admins/categories', []);
                        // dd($response);
        $response->assertStatus(422);
    }
}
