<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestRegistration;

use DateTime;

class SelfIsolationAddress
{
    public function __construct(
        private DateTime $dateOfArrival,
        private string   $travelNumber,
        private ?string  $nonExemptDay,
        private string   $countryCode,
    )
    {
    }

    public function getDateOfArrival(): DateTime
    {
        return $this->dateOfArrival;
    }

    public function setDateOfArrival(DateTime $dateOfArrival): void
    {
        $this->dateOfArrival = $dateOfArrival;
    }

    public function getTravelNumber(): string
    {
        return $this->travelNumber;
    }

    public function setTravelNumber(string $travelNumber): void
    {
        $this->travelNumber = $travelNumber;
    }

    public function getNonExemptDay(): ?string
    {
        return $this->nonExemptDay;
    }

    public function setNonExemptDay(?string $nonExemptDay): void
    {
        $this->nonExemptDay = $nonExemptDay;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }
}
