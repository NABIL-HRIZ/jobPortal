<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class LoginTest extends TestCase

{
    use RefreshDatabase;

    
    /**
     * A basic feature test example.
     */

    /** @test */
   
     public function test_user_can_login_with_correct_credentials()
    {

        $user = User::factory()->create([
            'email' => 'nabil@test.com',
            'password' => Hash::make('password123'),
        ]);


        $response = $this->postJson('/api/login', [
            'email' => 'nabil@test.com',
            'password' => 'password123',
        ]);


        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'token',
                     'user' => ['id','name','email','created_at','updated_at']
                 ]);
    }


    public function test_user_cannot_login_with_wrong_password()
    {
        $user = User::factory()->create([
            'email' => 'nabil@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'nabil@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Invalid credentials',
                 ]);
    }


    public function test_user_can_logout_successfully()
    {
        $user = User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Logged out successfully',
                 ]);
    }
}
