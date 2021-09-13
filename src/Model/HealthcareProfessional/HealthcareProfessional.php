<?php

declare(strict_types=1);

namespace LML\SDK\Model\HealthcareProfessional;

use LML\SDK\Attribute\Model;
use LML\SDK\Repository\HealthcareProfessionalRepository;
use function sprintf;

#[Model(repositoryClass: HealthcareProfessionalRepository::class)]
class HealthcareProfessional implements HealthcareProfessionalInterface
{
    public function __construct(
        protected string $id,
        protected string $firstName,
        protected string $lastName,
        protected bool   $isNurse,
        protected bool   $isLMLApproved,
    )
    {
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function isNurse(): bool
    {
        return $this->isNurse;
    }

    public function isLMLApproved(): bool
    {
        return $this->isLMLApproved;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray()
    {
        return [
            'id'              => $this->getId(),
            'first_name'      => $this->getFirstName(),
            'last_name'       => $this->getLastName(),
            'is_nurse'        => $this->isNurse(),
            'is_lml_approved' => $this->isLMLApproved(),
        ];
    }
}
