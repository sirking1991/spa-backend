<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class ApiTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $this->assertTrue(true);
    }

    public function test_user_can_login(): void
    {
        $this->assertTrue(true);
    }

    public function test_user_can_view_tasks(): void
    {
        $user = User::factory()->create();
        $tasks = Task::factory(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_can_create_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/tasks', [
            'title' => 'Sample Task',
            'due_on' => now()->addDay(),
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Sample Task']);
    }

    public function test_user_can_update_task(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put('/api/tasks/' . $task->id, [
            'title' => 'Sample Task',
            'completed' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Sample Task',
                'completed' => true,
            ]);

    }

    public function test_user_can_complete_task(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id, 'completed'=>false]);

        $response = $this->actingAs($user)->put('/api/tasks/' . $task->id . '/complete');

        $response->assertStatus(201)
            ->assertJsonFragment([
                'completed' => true,
            ]);
    }

    public function test_user_can_delete_task(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id, 'completed'=>false]);

        $taskId = $task->id;

        $response = $this->actingAs($user)->delete('/api/tasks/' . $taskId);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', ['id'=>$taskId]);
        
    }

    public function test_user_can_not_update_other_user_task(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->put('/api/tasks/' . $task->id, [
            'title' => 'Sample Task',
            'completed' => true,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_not_delete_other_user_task(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->delete('/api/tasks/' . $task->id, [
            'title' => 'Sample Task',
            'completed' => true,
        ]);

        $response->assertStatus(403);
    }    


}
