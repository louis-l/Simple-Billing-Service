<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Services\Billing\BillingManager;
use App\Services\Billing\Exceptions\BillingNotDueException;
use App\Services\Billing\Exceptions\NoSubscriptionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_subscribe_to_monthly_product_and_created_subscription_with_next_billing_date_to_next_month(): void
    {
        $this->travelTo('2024-10-07 20:00:00');

        $user = User::factory()->createOne();

        $product = Product::factory()->asMonthlyPeriod()->createOne();

        $subscription = resolve(BillingManager::class)
            ->setUser($user)
            ->subscribeToProduct($product);

        $this->assertequals('2024-11-07 20:00:00', $subscription->next_billing_at->toDateTimeString());
    }

    public function test_subscribe_to_yearly_product_and_created_subscription_with_next_billing_date_to_next_year(): void
    {
        $this->travelTo('2024-10-07 20:00:00');

        $user = User::factory()->createOne();

        $product = Product::factory()->asYearlyPeriod()->createOne();

        $subscription = resolve(BillingManager::class)
            ->setUser($user)
            ->subscribeToProduct($product);

        $this->assertequals('2025-10-07 20:00:00', $subscription->next_billing_at->toDateTimeString());
    }

    public function test_billing_a_user_without_subscription_will_throw_exception(): void
    {
        $user = User::factory()->createOne();

        $this->expectException(NoSubscriptionException::class);

        resolve(BillingManager::class)
            ->setUser($user)
            ->billNextInvoice();
    }

    public function test_billing_a_user_with_subscription_not_overdue_will_throw_exception(): void
    {
        $user = User::factory()->createOne();

        $product = Product::factory()->asMonthlyPeriod()->createOne();

        resolve(BillingManager::class)
            ->setUser($user)
            ->subscribeToProduct($product);

        $this->expectException(BillingNotDueException::class);

        resolve(BillingManager::class)
            ->setUser($user)
            ->billNextInvoice();
    }

    public function test_can_bill_user_and_subscription_next_billing_date_gets_updated(): void
    {
        $this->travelTo('2024-10-07 20:00:00');

        $user = User::factory()->createOne();

        $product = Product::factory()->asMonthlyPeriod()->createOne();

        $subscription = resolve(BillingManager::class)
            ->setUser($user)
            ->subscribeToProduct($product);
        $subscription->next_billing_at = now()->subDays(3);
        $subscription->saveQuietly();

        $invoice = resolve(BillingManager::class)
            ->setUser($user)
            ->billNextInvoice();

        $invoice->refresh();
        $this->assertFalse($invoice->is_paid);

        $subscription->refresh();
        $this->assertequals('2024-11-07 20:00:00', $subscription->next_billing_at->toDateTimeString());
    }
}
