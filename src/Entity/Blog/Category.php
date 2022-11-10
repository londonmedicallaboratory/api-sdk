<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Blog;

use Stringable;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Repository\Blog\CategoryRepository;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      slug: string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: CategoryRepository::class, baseUrl: 'blog/category')]
class Category implements ModelInterface, Stringable
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $slug,
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

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
        ];
    }
}
