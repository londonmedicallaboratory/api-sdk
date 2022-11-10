<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Page;

use Stringable;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Repository\PageRepository;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      slug: string,
 *      content: string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: PageRepository::class, baseUrl: 'page')]
class Page implements ModelInterface, Stringable
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $slug,
        protected string $content,
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'content' => $this->getContent(),
        ];
    }
}
