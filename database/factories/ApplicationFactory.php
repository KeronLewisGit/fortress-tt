<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '868-'.fake()->numerify('###-####'),
            'area' => fake()->randomElement(['Arima', 'Chaguanas', 'San Fernando', 'Tunapuna', 'Point Fortin', 'Scarborough']),
            'date_of_birth' => fake()->dateTimeBetween('-45 years', '-19 years'),
            'licence_class' => fake()->randomElement(['none', '3', '4', '5']),
            'has_police_certificate' => fake()->boolean(70),
            'years_experience' => fake()->numberBetween(0, 12),
            'source' => 'website',
            'status' => 'new',
        ];
    }
}
