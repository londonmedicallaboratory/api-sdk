<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Blog;

use LML\SDK\Attribute\Entity;
use LML\SDK\Repository\Blog\ArticleRepository;

#[Entity(repositoryClass: ArticleRepository::class, baseUrl: 'blog/article')]
class Article implements ArticleInterface
{
    public function __construct(
        protected string $id,
        protected string $title,
        protected string $content,
        protected string $slug,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function toArray()
    {
        return [
            'id'      => $this->getId(),
            'title'   => $this->getTitle(),
            'slug'    => $this->getSlug(),
            'content' => $this->getContent(),
        ];
    }
}
