<?php

declare(strict_types=1);

namespace LML\SDK\DTO;

use Closure;
use LML\SDK\Model\Money\PriceInterface;
use LML\SDK\Model\Shipping\ShippingInterface;
use LML\SDK\Exception\PaymentFailureException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @noinspection TypoSafeNamingInspection
 */
class Payment
{
    /**
     * @param null|Closure(PaymentFailureException): ?Response $paymentExceptionHandler
     */
    public function __construct(
        public ?string $id = null,
        public ?PriceInterface $price = null,
        public ?string $customersEmail = null,

        public ?string $customersFirstName = null,
        public ?string $customersLastName = null,
        public ?string $customersPhoneNumber = null,
        public ?string $customersCompany = null,
        public ?string $customersAddressLine1 = null,
        public ?string $customersAddressLine2 = null,
        public ?string $customersAddressLine3 = null,
        public ?string $customersPostalCode = null,
        public ?string $customersCity = null,
        public ?string $customersCountry = null,

        public ?string $deliveryFirstName = null,
        public ?string $deliveryLastName = null,
        public ?string $deliveryPhoneNumber = null,
        public ?string $deliveryCompany = null,
        public ?string $deliveryAddressLine1 = null,
        public ?string $deliveryAddressLine2 = null,
        public ?string $deliveryAddressLine3 = null,
        public ?string $deliveryPostalCode = null,
        public ?string $deliveryCity = null,
        public ?string $deliveryCountry = null,

        public ?string $cardFirstName = null,
        public ?string $cardLastName = null,
        public ?string $cardNumber = null,
        public ?string $cardCVV = null,
        public ?string $cardExpirationDate = null,

        public ?string $successUrl = null,
        public ?string $failureUrl = null,
        public ?string $notifyUrl = null,
        public ?string $paymentConfirmationUrl = null,

        public ?ShippingInterface $shipping = null,

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
