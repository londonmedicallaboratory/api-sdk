<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Biomarker;

use LML\SDK\Attribute\Entity;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\BiomarkerRepository;
use LML\SDK\Entity\Category\CategoryInterface;

#[Entity(repositoryClass: BiomarkerRepository::class, baseUrl: 'biomarker')]
class Biomarker implements BiomarkerInterface
{
    /**
     * @param LazyValueInterface<CategoryInterface> $category
     */
    public function __construct(
        protected string             $id,
        protected string             $name,
        protected string             $slug,
        protected string             $code,
        protected ?string            $description,
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

    public function getCategory(): CategoryInterface
    {
        return $this->category->getValue();
    }

    public function getTestTypes()
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
            'id'          => $this->getId(),
            'name'        => $this->getName(),
            'code'        => $this->getCode(),
            'slug'        => $this->getSlug(),
            'description' => $this->getDescription(),
        ];
    }
}
