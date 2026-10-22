<?php

namespace Database\Factories;

use App\Models\JobPosting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JobPostingFactory extends Factory
{
    protected $model = JobPosting::class;

    public function definition(): array
    {
        $title = fake()->randomElement(['Security Officer', 'Baton Officer', 'Security Driver', 'CIT Officer']).' '.fake()->unique()->numberBetween(1, 999);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'department' => 'Operations',
            'location' => 'East-West Corridor',
            'employment_type' => 'full_time',
            'summary' => fake()->sentence(12),
            'description' => fake()->paragraph(),
            'requirements' => ['Valid Police Certificate of Character', 'Physically fit'],
            'status' => 'open',
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft', 'published_at' => null]);
    }
}
