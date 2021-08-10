<?php

declare(strict_types=1);

namespace LML\SDK\Model\Biomarker;

use LML\View\Lazy\LazyValue;
use LML\SDK\Model\Category\CategoryInterface;

class Biomarker implements BiomarkerInterface
{
    /**
     * @param LazyValue<CategoryInterface> $category
     */
    public function __construct(
        private string $id,
        private string $name,
        private string $slug,
        private string $code,
        private ?string $description,
        private LazyValue $category,
    )
    {
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
            'id' => $this->getId(),
            'name' => $this->getName(),
            'code' => $this->getCode(),
            'slug' => $this->getSlug(),
            'description' => $this->getDescription(),
        ];
    }
}
