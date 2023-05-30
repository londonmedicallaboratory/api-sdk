<?php

declare(strict_types=1);

namespace LML\SDK\DTO;

use Closure;
use LML\SDK\Entity\Address\Address;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Exception\PaymentFailureException;
use Symfony\Component\HttpFoundation\Response;

class Payment
{
    /**
     * @param null|Closure(PaymentFailureException): ?Response $paymentExceptionHandler
     */
    public function __construct(
        public ?PriceInterface $price = null,
        public ?string $id = null,

        public ?string $cardFirstName = null,
        public ?string $cardLastName = null,
        public ?string $cardNumber = null,
        public ?string $cardCVV = null,
        public ?string $cardExpirationDate = null,
        public ?Address $billingAddress = null,

        public ?string $successUrl = null,
        public ?string $failureUrl = null,
        public ?string $notifyUrl = null,
        public ?string $paymentConfirmationUrl = null,

        public ?Closure $paymentExceptionHandler = null,

        public bool $isTest = true,
    )
    {
        if ($isTest) {
            $this->populateTestData();
        }
    }

    /**
     * @see https://www.opayo.co.uk/support/12/36/test-card-details-for-your-test-transactions
     */
    private function populateTestData(): void
    {
        $this->cardNumber = '4929000005559';
//            $this->cardNumber = '4929000000006';
        $this->cardExpirationDate = '12/25';
        $this->cardCVV = '123';
    }
}
