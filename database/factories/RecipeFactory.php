<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence,
            'type' => fake()->randomElement(['dessert','appetizer','main-dish']),
            'ingredients' => fake()->sentences(rand(2,10), true),
            'instruction' => fake()->sentences(rand(2,10), true),
        ];
    }
}
