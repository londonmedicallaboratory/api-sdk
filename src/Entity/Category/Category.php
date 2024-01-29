<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Category;

use Stringable;
use LML\SDK\Entity\File\File;
use LML\SDK\Entity\ModelInterface;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\SluggableInterface;
use LML\SDK\Repository\ProductCategoryRepository;
use LML\SDK\Repository\BiomarkerCategoryRepository;

/**
 * @psalm-type S=array{
 *     id: string,
 *     name: string,
 *     nr_of_products?: int,
 *     slug: string,
 *     description: ?string,
 *     logo_id?: ?string,
 *     icon_id?: ?string,
 *     color?: ?string,
 * }
 *
 * @implements ModelInterface<S>
 */
class Category implements ModelInterface, SluggableInterface, Stringable
{
    /**
     * @see ProductCategoryRepository::one()
     * @see BiomarkerCategoryRepository::one()
     *
     * @param LazyValueInterface<?int> $nrOfProducts
     * @param LazyValueInterface<?File> $logo
     * @param LazyValueInterface<?File> $icon
     */
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $slug,
        protected LazyValueInterface $nrOfProducts,
        protected ?string $description,
        protected LazyValueInterface $logo,
        protected LazyValueInterface $icon,
        protected LazyValueInterface $color,
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

    public function getLogo(): ?File
    {
        return $this->logo->getValue();
    }

    public function getIcon(): ?File
    {
        return $this->icon->getValue();
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'description' => $this->getDescription(),
        ];
        $nrOfProducts = $this->getNrOfProducts();

        if (null !== $nrOfProducts) {
            $data['nr_of_products'] = $nrOfProducts;
        }

        return $data;
    }
}
