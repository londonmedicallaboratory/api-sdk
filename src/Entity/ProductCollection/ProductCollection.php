<?php

declare(strict_types=1);

namespace LML\SDK\Entity\ProductCollection;

use LML\SDK\Attribute\Entity;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\File\FileInterface;
use LML\SDK\Repository\ProductCollectionRepository;

#[Entity(repositoryClass: ProductCollectionRepository::class, baseUrl: 'product_collection')]
class ProductCollection implements ProductCollectionInterface
{
    /**
     * @param LazyValueInterface<?FileInterface> $logo
     */
    public function __construct(
        protected string             $id,
        protected string             $name,
        protected string             $slug,
        protected string             $description,
        protected LazyValueInterface $logo,
    )
    {
    }

    public function getLogo(): ?FileInterface
    {
        return $this->logo->getValue();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->getId(),
            'name'        => $this->getName(),
            'slug'        => $this->getSlug(),
            'description' => $this->getDescription(),
        ];
    }
}
