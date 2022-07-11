<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Category;

use LML\SDK\Attribute\Entity;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\ProductCategoryRepository;

#[Entity(repositoryClass: ProductCategoryRepository::class, baseUrl: 'product_categories')]
class Category implements CategoryInterface
{
    /**
     * @param LazyValueInterface<?int> $nrOfProducts
     */
    public function __construct(
        protected string             $id,
        protected string             $name,
        protected string             $slug,
        protected LazyValueInterface $nrOfProducts,
        protected ?string            $description,
    )
    {
    }

    public function __toString(): string
    {
        return $this->getName();
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

    public function getBiomarkers(): array
    {
        return [];
    }

    public function getNrOfProducts(): ?int
    {
        return $this->nrOfProducts->getValue();
    }

    public function toArray(): array
    {
        $data = [
            'id'          => $this->getId(),
            'name'        => $this->getName(),
            'slug'        => $this->getSlug(),
            'description' => $this->getDescription(),
        ];
        $nrOfProducts = $this->getNrOfProducts();

        if (null !== $nrOfProducts) {
            $data['nr_of_products'] = $nrOfProducts;
        }

        return $data;
    }
}
