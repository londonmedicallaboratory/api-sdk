<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

class LabResult implements LabResultInterface
{
    public function __construct(
        protected string  $id,
        protected string  $name,
        protected string  $code,
        protected string  $value,
        protected ?string $minRange,
        protected ?string $maxRange,
    )
    {
    }

    public function getMinRange(): ?string
    {
        return $this->minRange;
    }

    public function getMaxRange(): ?string
    {
        return $this->maxRange;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray()
    {
        return [
            'id'        => $this->getId(),
            'name'      => $this->getName(),
            'code'      => $this->getCode(),
            'value'     => $this->getValue(),
            'min_range' => $this->getMinRange(),
            'max_range' => $this->getMaxRange(),
        ];
    }
}
