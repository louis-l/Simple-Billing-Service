<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Services\Billing\BillingManager;
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

        resolve(BillingManager::class)
            ->setUser($user1)
            ->subscribeToProduct($productMonthly);

        resolve(BillingManager::class)
            ->setUser($user2)
            ->subscribeToProduct($productYearly);
    }
}
