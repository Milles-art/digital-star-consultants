<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'service_category_id' => ServiceCategory::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 0, 500000),
            'is_active' => true,
            'sort_order' => 0,
            'duration_minutes' => fake()->randomElement([30, 60, 90, 120]),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function free(): static
    {
        return $this->state(fn () => ['price' => 0]);
    }
}
