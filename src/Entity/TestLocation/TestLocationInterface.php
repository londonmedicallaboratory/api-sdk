<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation;

use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\HealthcareProfessional\HealthcareProfessionalInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      full_address: string,
 *      city: string,
 *      postal_code: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface TestLocationInterface extends ModelInterface
{
    public function getName(): string;

    public function getFullAddress(): string;

    public function getCity(): string;

    public function getPostalCode(): string;

    /**
     * @return list<HealthcareProfessionalInterface>
     */
    public function getHealthcareProfessionals();
}
