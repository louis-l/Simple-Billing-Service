<?php

namespace Database\Factories;

use App\Enums\ProductPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(50, 200),
            'period' => $this->faker->randomElement(ProductPeriod::values()),
        ];
    }

    public function asMonthlyPeriod(): static
    {
        return $this->state(fn () => [
            'period' => ProductPeriod::monthly,
        ]);
    }

    public function asYearlyPeriod(): static
    {
        return $this->state(fn () => [
            'period' => ProductPeriod::yearly,
        ]);
    }
}
