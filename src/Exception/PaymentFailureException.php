<?php

declare(strict_types=1);

namespace LML\SDK\Exception;

use Throwable;
use RuntimeException;

class PaymentFailureException extends RuntimeException
{
    public function __construct(?string $message, Throwable $previous = null)
    {
        parent::__construct($message ?? 'Payment failure, but no message provided.', 0, $previous);
    }
}
