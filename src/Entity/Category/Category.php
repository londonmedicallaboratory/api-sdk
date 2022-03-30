<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Category;

use LML\SDK\Attribute\Entity;
use LML\SDK\Repository\ProductCategoryRepository;

#[Entity(repositoryClass: ProductCategoryRepository::class)]
class Category implements CategoryInterface
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $slug,
        protected ?string $description,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getBiomarkers()
    {
        return [];
    }

    public function toArray()
    {
        return [
            'id'          => $this->getId(),
            'name'        => $this->getName(),
            'slug'        => $this->getSlug(),
            'description' => $this->getDescription(),
        ];
    }
}