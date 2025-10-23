<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class RegisterTest extends TestCase
{
    use RefreshDatabase; // not gonna refresh the real data i already add  db:memory in .env.testing

    
     /** @test */
    public function user_can_register_successfully()
    {
        Role::create(['name' => 'user']);


        $data = [
            'name' => 'Nabil HRIZ',
            'email' => 'nabil@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];


        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => ['id','name','email','created_at','updated_at'],
                 ]);


        // Check that user exists i
        $this->assertDatabaseHas('users', [
            'email' => 'nabil@test.com'
        ]);
    }

     /** @test */


    public function registration_fails_if_passwords_do_not_match()
    {
        $data = [
            'name' => 'Fake User',
            'email' => 'fake@test.com',
            'password' => 'password123',
            'password_confirmation' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
    }
}
