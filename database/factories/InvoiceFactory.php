<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(50, 200),
            'is_paid' => $this->faker->boolean(),
        ];
    }

    public function asPaid(bool $paid = true): static
    {
        return $this->state(fn () => ['is_paid' => $paid]);
    }
}
