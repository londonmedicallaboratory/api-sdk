<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Blog;

use LML\SDK\Attribute\Entity;
use LML\SDK\Repository\Blog\CategoryRepository;

#[Entity(repositoryClass: CategoryRepository::class, baseUrl: 'blog/category')]
class Category implements CategoryInterface
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $slug,
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

    public function toArray()
    {
        return [
            'id'   => $this->getId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
        ];
    }
}
