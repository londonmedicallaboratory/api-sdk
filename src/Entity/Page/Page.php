<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Page;

use LML\SDK\Attribute\Entity;
use LML\SDK\Repository\PageRepository;

#[Entity(repositoryClass: PageRepository::class, baseUrl: 'page')]
class Page implements PageInterface
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $slug,
        protected string $content,
    )
    {
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

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'content' => $this->getContent(),
        ];
    }
}
