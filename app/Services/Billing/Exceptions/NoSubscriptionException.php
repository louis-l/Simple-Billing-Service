<?php

namespace App\Services\Billing\Exceptions;

class NoSubscriptionException extends BillingException
{
    protected $code = 403;
    protected $message = 'Subscription not found.';
}
