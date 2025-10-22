<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use Laravel\Sanctum\Sanctum;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
{
    parent::setUp();

    foreach (['user', 'employeur', 'admin'] as $roleName) {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName]);
    }
}

    

    /** @test */
    public function test_authenticated_user_can_get_his_personal_data()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/personal-data');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'user' => [
                         'id' => $user->id,
                         'name' => $user->name,
                         'email' => $user->email,
                     ]
                 ]);
    }

    /** @test */
    public function test_authenticated_user_can_store_an_application()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $job = Job::factory()->create();

        Sanctum::actingAs($user);

        $data = [
            'job_id' => $job->id,
            'phone_number' => '0600000000',
            'cover_letter' => 'Je suis motivé pour ce poste.',
            'resume_path' => 'uploads/resume.pdf',
        ];

        $response = $this->postJson('/api/applications', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Application submitted successfully',
                 ]);

        $this->assertDatabaseHas('applications', [
            'job_id' => $job->id,
            'phone_number' => '0600000000',
        ]);
    }

    /** @test */
    public function test_user_can_get_own_applications()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $job = Job::factory()->create();

        $user->applications()->create([
            'job_id' => $job->id,
            'phone_number' => '0611111111',
            'cover_letter' => 'Cover letter text',
            'resume_path' => 'uploads/test.pdf',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/applications');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'applications' => [
                         ['id', 'job_id', 'phone_number', 'cover_letter', 'resume_path']
                     ]
                 ]);
    }
}
