<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use Spatie\Permission\Models\Role;

class JobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['user', 'employeur', 'admin'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }

    /** @test */
    public function employeur_or_admin_can_create_job()
    {
        $employeur = User::factory()->create();
        $employeur->assignRole('employeur');

        $payload = [
            'title' => 'Développeur Laravel',
            'description' => 'Backend Laravel API REST',
            'company_name' => 'Tech Corp',
            'location' => 'Casablanca',
            'employment_type' => 'full-time',
            'salary' => 8000,
        ];

        $response = $this->actingAs($employeur)->postJson('/api/jobs', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Job posted successfully',
                 ]);

        $this->assertDatabaseHas('jobs', [
            'title' => 'Développeur Laravel',
            'posted_by' => $employeur->id
        ]);
    }

    /** @test */
    public function index_returns_paginated_jobs_with_employer()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Job::factory(15)->create([
            'posted_by' => $admin->id
        ]);

        $response = $this->actingAs($admin)->getJson('/api/jobs');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'jobs' => [
                         'current_page',
                         'data' => [
                             [
                                 'id',
                                 'title',
                                 'description',
                                 'company_name',
                                 'location',
                                 'employment_type',
                                 'salary',
                                 'posted_by',
                                 'employer' => ['id', 'name', 'email']
                             ]
                         ],
                         'last_page',
                     ]
                 ]);

        $this->assertCount(10, $response->json('jobs.data')); 
    }

    /** @test */
    public function employeur_or_admin_can_update_job()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $job = Job::factory()->create([
            'title' => 'Old Title',
            'posted_by' => $admin->id
        ]);

        $response = $this->actingAs($admin)->putJson("/api/jobs/{$job->id}", [
            'title' => 'New Title'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Job updated successfully',
                     'job' => [
                         'id' => $job->id,
                         'title' => 'New Title',
                     ]
                 ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'New Title'
        ]);
    }

    /** @test */
    public function employeur_or_admin_can_delete_job()
    {
        $employeur = User::factory()->create();
        $employeur->assignRole('employeur');

        $job = Job::factory()->create([
            'posted_by' => $employeur->id
        ]);

        $response = $this->actingAs($employeur)->deleteJson("/api/jobs/{$job->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Job deleted successfully'
                 ]);

        $this->assertDatabaseMissing('jobs', [
            'id' => $job->id
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_update_or_delete_jobs()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $job = Job::factory()->create();

        // Create
        $createResponse = $this->actingAs($user)->postJson('/api/jobs', [
            'title' => 'Test',
            'description' => 'Test',
            'company_name' => 'Test',
            'location' => 'Test',
            'employment_type' => 'full-time'
        ]);
        $createResponse->assertStatus(403);

        // Update
        $updateResponse = $this->actingAs($user)->putJson("/api/jobs/{$job->id}", [
            'title' => 'New Title'
        ]);
        $updateResponse->assertStatus(403);

        // Delete
        $deleteResponse = $this->actingAs($user)->deleteJson("/api/jobs/{$job->id}");
        $deleteResponse->assertStatus(403);
    }
}
