<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Category;

use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\File\FileInterface;
use LML\SDK\Repository\ProductCategoryRepository;
use LML\SDK\Repository\BiomarkerCategoryRepository;

class Category implements CategoryInterface
{
    /**
     * @see ProductCategoryRepository::one()
     * @see BiomarkerCategoryRepository::one()
     *
     * @param LazyValueInterface<?int> $nrOfProducts
     * @param LazyValueInterface<?FileInterface> $logo
     */
    public function __construct(
        protected string             $id,
        protected string             $name,
        protected string             $slug,
        protected LazyValueInterface $nrOfProducts,
        protected ?string            $description,
        protected LazyValueInterface $logo,
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

    public function getLogo(): ?FileInterface
    {
        return $this->logo->getValue();
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
