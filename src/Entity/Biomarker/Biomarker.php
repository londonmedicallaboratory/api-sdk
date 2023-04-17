<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Biomarker;

use Stringable;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Category\Category;
use LML\SDK\Repository\BiomarkerRepository;

/**
 * @psalm-type S=array{
 *     id: string,
 *     name: string,
 *     code: string,
 *     slug: string,
 *     description: ?string,
 *     category_id: string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: BiomarkerRepository::class, baseUrl: 'biomarker')]
class Biomarker implements ModelInterface, Stringable
{
    /**
     * @param LazyValueInterface<Category> $category
     */
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $slug,
        protected string $code,
        protected ?string $description,
        protected LazyValueInterface $category,
    )
    {
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCategory(): Category
    {
        return $this->category->getValue();
    }

    /**
     * @return list<TestTypeInterface>
     */
    public function getTestTypes(): array
    {
        return [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'code' => $this->getCode(),
            'slug' => $this->getSlug(),
            'description' => $this->getDescription(),
            'category_id' => $this->getCategory()->getId(),
        ];
    }
}
