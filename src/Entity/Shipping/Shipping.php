<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Shipping;

use Stringable;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Repository\ShippingRepository;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      type: string,
 *      description: ?string,
 *      price: array{amount_minor: int, currency: string, formatted_value: string},
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: ShippingRepository::class, baseUrl: 'shipping')]
class Shipping implements ModelInterface, Stringable
{
    /**
     * @see ShippingRepository::one()
     */
    public function __construct(
        protected string $id,
        protected string $type,
        protected string $name,
        protected ?string $description,
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
            'id' => $this->getId(),
            'type' => $this->getType(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'price' => $this->getPrice()->toArray(),
        ];
    }
}
