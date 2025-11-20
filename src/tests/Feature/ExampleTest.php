<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_example()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // ログイン後のトップページへアクセス
        $response = $this->get('/attendance');

        $response->assertStatus(200);
    }
}
