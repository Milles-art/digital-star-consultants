<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'customer_email' => fake()->safeEmail(),
            'customer_notes' => fake()->optional()->sentence(),
            'preferred_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'status' => Submission::STATUS_PENDING,
            'payment_status' => Submission::PAYMENT_PENDING,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => Submission::STATUS_PENDING]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => Submission::STATUS_IN_PROGRESS]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => Submission::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function assignedTo(User $user): static
    {
        return $this->state(fn () => [
            'processed_by' => $user->id,
            'status' => Submission::STATUS_IN_PROGRESS,
        ]);
    }
}
