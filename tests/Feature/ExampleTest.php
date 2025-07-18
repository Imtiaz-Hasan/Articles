<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_and_create_article()
    {
        $this->seed();

        $user = User::where('email', 'test@example.com')->first();
        $category = Category::first();
        $login = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $login->assertStatus(200)->assertJsonStructure(['token', 'user']);
        $token = $login->json('token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/articles', [
                'title' => 'Test Article',
                'body' => 'Test body',
                'status' => 'published',
                'category_id' => $category->id,
            ]);
        $response->assertStatus(201)->assertJsonFragment(['title' => 'Test Article']);
    }
}
