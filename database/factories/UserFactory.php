<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fullName = fake()->name();
        $nameParts = explode(' ', $fullName, 2);
        
        return [
            'rol_id' => 1, // Default role
            'emri' => $nameParts[0],
            'mbiemri' => $nameParts[1] ?? '',
            'email' => fake()->unique()->safeEmail(),
            'fjalekalimi_hash' => static::$password ??= Hash::make('password'),
            'telefon' => fake()->phoneNumber(),
            'adresa' => fake()->address(),
        ];
    }


}
