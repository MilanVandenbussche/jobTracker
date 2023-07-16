<?php

namespace Database\Factories;

use App\Models\Languages;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $languages = Languages::all()->pluck('id')->toArray();
        $firstname = fake()->firstName();
        $lastname = fake()->lastName();
        return [
            'first_name' => $firstname,
            'last_name' => $lastname,
            'email' => strtolower($firstname) . "." . strtolower($lastname) . "@example.com",
            'language_id' => $languages[rand(0, count($languages) - 1)],
            'admin' => false,
            'password' => bcrypt('test12345678'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
