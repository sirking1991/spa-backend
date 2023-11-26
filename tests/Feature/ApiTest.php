<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Recipe;
use App\Models\User;
use Tests\TestCase;

class ApiTest extends TestCase
{

    use RefreshDatabase;

    protected $sampleRecipe = [
        'name' => 'Sample Recipe',
        'type' => 'dessert',
        'ingredients' => 'The ingredients',
        'instruction' => 'How to cook',
    ];

    public function test_user_can_register(): void
    {
        $response = $this->post('/api/register', $this->sampleRecipe);

        $response->assertStatus(201);
    }

    public function test_user_can_login(): void
    {
        $this->test_user_can_register();

        $response = $this->post('/api/login', [
            'email' => 'test@email.com',
            'password' => 'password',
        ]);

        $response->assertStatus(201);
    }

    public function test_user_can_view_recipes(): void
    {
        $user = User::factory()->create();
        $recipes = Recipe::factory(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/api/recipes');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_can_create_recipe(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/recipes', $this->sampleRecipe);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Sample Recipe']);
    }

    public function test_user_can_update_recipe(): void
    {
        $user = User::factory()->create();

        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put('/api/recipes/' . $recipe->id, $this->sampleRecipe);

        $response->assertStatus(201)
            ->assertJsonFragment($this->sampleRecipe);

    }


    public function test_user_can_delete_recipe(): void
    {
        $user = User::factory()->create();

        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $recipeId = $recipe->id;

        $response = $this->actingAs($user)->delete('/api/recipes/' . $recipeId);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('recipes', ['id'=>$recipeId]);
        
    }

    public function test_user_can_not_update_other_user_recipe(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $recipe = Recipe::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->put('/api/recipes/' . $recipe->id, $this->sampleRecipe);

        $response->assertStatus(403);
    }

    public function test_user_can_not_delete_other_user_recipe(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $recipe = Recipe::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->delete('/api/recipes/' . $recipe->id, $this->sampleRecipe);

        $response->assertStatus(403);
    }    


}
