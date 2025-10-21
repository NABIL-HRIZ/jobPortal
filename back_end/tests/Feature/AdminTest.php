<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_all_users_with_roles()
    {
        $roles = ['user', 'employeur', 'admin'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $user1 = User::factory()->create();
        $user1->assignRole('user');

        $user2 = User::factory()->create();
        $user2->assignRole('employeur');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->getJson('/api/admin/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'users' => [
                         [
                             'id',
                             'name',
                             'email',
                             'roles',
                         ]
                     ]
                 ]);
    }

    /** @test */
    public function it_returns_personal_data_with_roles()
    {
        $role = Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->getJson('/api/admin/personal-data');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'user' => [
                         'id',
                         'name',
                         'email',
                         'roles'
                     ]
                 ]);

        $this->assertEquals('admin', $response->json('user.roles.0'));
    }

    /** @test */
    public function it_updates_user_data_successfully()
    {
        $role = Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($admin)->putJson("/api/admin/users/{$user->id}", [
            'name' => 'New Name',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'User data updated successfully',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }


    /** @test */
public function it_deletes_a_user_successfully()
{
    $role = Role::create(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');


    $userToDelete = User::factory()->create();

    $response = $this->actingAs($admin)->deleteJson("/api/admin/users/{$userToDelete->id}");


    $response->assertStatus(200)
             ->assertJson([
                 'status' => 'success',
                 'message' => 'User deleted successfully',
             ]);

    
    $this->assertDatabaseMissing('users', [
        'id' => $userToDelete->id,
    ]);
}

}
