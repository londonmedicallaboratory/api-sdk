<?php

declare(strict_types=1);

namespace LML\SDK\Model\Shipping;

class Shipping implements ShippingInterface
{
    public function __construct(
        private string $id,
        private string $type,
        private string $name,
        private ?string $description,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function toArray()
    {
        return [
            'id'          => $this->getId(),
            'type'        => $this->getType(),
            'name'        => $this->getName(),
            'description' => $this->getDescription(),
        ];
    }
}
