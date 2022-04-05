<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

class LabResult implements LabResultInterface
{
    public function __construct(
        protected string  $id,
        protected string  $name,
        protected string  $value,
        protected ?string $minValue,
        protected ?string $maxValue,
    )
    {
    }

    public function getMinRange(): ?string
    {
        return $this->minValue;
    }

    public function getMaxRange(): ?string
    {
        return $this->maxValue;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
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
            'value'     => $this->getValue(),
            'min_range' => $this->getMinRange(),
            'max_range' => $this->getMaxRange(),
        ];
    }
}
