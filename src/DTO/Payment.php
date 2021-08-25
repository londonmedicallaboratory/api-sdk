<?php

declare(strict_types=1);

namespace LML\SDK\DTO;

use LML\SDK\Model\Money\PriceInterface;
use LML\SDK\Model\Shipping\ShippingInterface;

/**
 * @noinspection TypoSafeNamingInspection
 */
class Payment
{
    public function __construct(
        public ?string $id = null,
        public ?PriceInterface $price = null,
        public ?string $customersFirstName = null,
        public ?string $customersLastName = null,
        public ?string $customersEmail = null,
        public ?string $customersPhoneNumber = null,
        public ?string $customersCompany = null,

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

        public bool $isTest = true,
    )
    {
        if ($isTest) {
            $this->populateTestData();
        }
    }

    private function populateTestData(): void
    {
        $this->cardNumber = '4929 0000 0555 9';
//            $this->cardNumber = '4929 0000 0000 6';
        $this->cardExpirationDate = '12/25';
        $this->cardCVV = '123';
    }
}
