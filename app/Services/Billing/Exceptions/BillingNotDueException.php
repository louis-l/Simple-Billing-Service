<?php

namespace App\Services\Billing\Exceptions;

class BillingNotDueException extends BillingException
{
    protected $code = 403;
    protected $message = 'Subscription billing date is not yet due.';
}
