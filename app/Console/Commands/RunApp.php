<?php

namespace App\Console\Commands;

use App\Enums\ProductPeriod;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Billing\BillingManager;
use Illuminate\Console\Command;

class RunApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the application in CLI';

    public function handle(): void
    {
        $availableSelectOptions = [
            0 => 'Print out all users info',
            1 => 'Create new user and subscribe to monthly product',
            2 => 'Create new user and subscribe to yearly product',
            3 => 'Create new user and subscribe to overdue monthly product',
            4 => 'Create new user and subscribe to overdue yearly product',
            5 => 'Check past due subscriptions and issue new invoices',
        ];

        $selectedOption = $this->choice(
            'Select option 0-4 to continue',
            $availableSelectOptions,
        );

        // It is easier to check against the index then the text, so we will use this to determine which logic to run.
        $selectedOptionIndex = collect($availableSelectOptions)->search($selectedOption);

        match ($selectedOptionIndex) {
            0 => $this->printUsers(),
            5 => $this->checkPastDueSubscriptionsAndIssueInvoices(),
            1, 2, 3, 4 => $this->handleSubscription($selectedOptionIndex),
            // Laravel is already handling invalid selection, so we dont need to worry about it here.
        };

        $this->info('Done');
    }

    protected function printUsers(): void
    {
        $data = User::query()
            ->with('subscription.product')
            ->get();

        $this->table(
            ['ID', 'Name', 'Email', 'Subscription', 'Price', 'Next billing date', 'Past due'],
            $data->map(function (User $user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->subscription->product->name,
                    $user->subscription->product->price,
                    $user->subscription->next_billing_at,
                    $user->subscription->is_next_billing_date_due ? 'Y' : 'N',
                ];
            })
        );
    }

    protected function checkPastDueSubscriptionsAndIssueInvoices(): void
    {
        Subscription::query()
            ->with('user')
            ->whereDate('next_billing_at', '<=', now())
            ->each(function (Subscription $subscription) {
                $invoice = resolve(BillingManager::class)
                    ->setUser($subscription->user)
                    ->billNextInvoice();

                $this->comment(
                    sprintf(
                        'Issued new invoice ID %d for user ID %d',
                        $invoice->id,
                        $subscription->user->id,
                    )
                );
            });
    }

    protected function handleSubscription(int $selectedOptionIndex): void
    {
        $user = User::factory()->createOne();

        $product = match ($selectedOptionIndex) {
            1, 3 => Product::query()->where('period', ProductPeriod::monthly)->firstOrFail(),
            2, 4 => Product::query()->where('period', ProductPeriod::yearly)->firstOrFail(),
        };

        $subscription = resolve(BillingManager::class)
            ->setUser($user)
            ->subscribeToProduct($product);

        if (in_array($selectedOptionIndex, [3, 4], true)) {
            $subscription->next_billing_at = now()->subDays(random_int(1, 30));
            $subscription->saveQuietly();
        }

        $this->comment(
            sprintf(
                'Created user ID %d and subscription ID %d, with next billing date is %s',
                $user->id,
                $subscription->id,
                $subscription->next_billing_at,
            )
        );
    }
}
