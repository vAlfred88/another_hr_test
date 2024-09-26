<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\History>
 */
class HistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'model_id' => Str::uuid(),
            'model_name' => 'User', // Для тестов будем использовать User
            'before' => json_encode([
                'name' => $this->faker->name,
                'email' => $this->faker->email,
            ]),
            'after' => json_encode([
                'name' => $this->faker->name,
                'email' => $this->faker->email,
            ]),
            'action' => 'updated',
        ];
    }
}
