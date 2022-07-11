<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Shipping;

use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Repository\ShippingRepository;

#[Entity(repositoryClass: ShippingRepository::class, baseUrl: 'shipping')]
class Shipping implements ShippingInterface
{
    /**
     * @see ShippingRepository::one()
     */
    public function __construct(
        protected string         $id,
        protected string         $type,
        protected string         $name,
        protected ?string        $description,
        protected PriceInterface $price,
    )
    {
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getPrice(): PriceInterface
    {
        return $this->price;
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

    public function toArray(): array
    {
        return [
            'id'          => $this->getId(),
            'type'        => $this->getType(),
            'name'        => $this->getName(),
            'description' => $this->getDescription(),
            'price'       => $this->getPrice()->toArray(),
        ];
    }
}
