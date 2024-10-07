<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user1 = User::firstOrCreate(
            ['email' => 'demo1@demo.com'],
            [
                'name' => 'Demo 1',
                'password' => 'password',
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'demo2@demo.com'],
            [
                'name' => 'Demo 2',
                'password' => 'password',
            ]
        );

        $productMonthly = Product::factory()
            ->state(['name' => 'Product 1'])
            ->asMonthlyPeriod()
            ->createOne();

        $productYearly = Product::factory()
            ->state(['name' => 'Product 2'])
            ->asYearlyPeriod()
            ->createOne();

        $subscriptionForUser1 = Subscription::factory()
            ->for($user1)
            ->for($productMonthly)
            ->state(['next_billing_at' => now()->addMonth()])
            ->createOne();

        $subscriptionForUser2 = Subscription::factory()
            ->for($user2)
            ->for($productYearly)
            ->state(['next_billing_at' => now()->addYear()])
            ->createOne();
    }
}
