<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_login_via_web_form()
    {
        $response = $this->postJson('/login', [
            'email' => 'admin@supa.ac.tz',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['redirect_url', 'user']);

        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_password()
    {
        $response = $this->postJson('/login', [
            'email' => 'admin@supa.ac.tz',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(422);
        $this->assertGuest();
    }

    public function test_user_can_login_via_phone_number()
    {
        $response = $this->postJson('/login', [
            'email' => '+255711000001',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['redirect_url', 'user']);

        $this->assertAuthenticated();
    }
}
