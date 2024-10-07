<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Billing\Exceptions\BillingNotDueException;
use App\Services\Billing\Exceptions\NoSubscriptionException;

class BillingManager
{
    protected User $user;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function subscribeToProduct(Product $product): Subscription
    {
        $subscription = new Subscription();
        $subscription->user()->associate($this->getUser());
        $subscription->product()->associate($product);
        $subscription->next_billing_at = $product->is_billed_monthly
            ? now()->addMonth()
            : now()->addYear();
        $subscription->save();

        return $subscription;
    }

    public function billNextInvoice(): Invoice
    {
        $user = $this->getUser();
        $subscription = $user->subscription;

        if (! $subscription) {
            throw new NoSubscriptionException();
        }

        if (! $subscription->is_next_billing_date_due) {
            throw new BillingNotDueException();
        }

        $invoice = new Invoice();
        $invoice->subscription()->associate($subscription);
        $invoice->amount = $subscription->product->price;
        $invoice->save();

        return $invoice;
    }
}
