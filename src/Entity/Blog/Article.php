<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Blog;

use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\File\File;
use LML\SDK\Entity\ModelInterface;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\Blog\ArticleRepository;

/**
 * @psalm-type S=array{
 *      id: string,
 *      title: string,
 *      slug: string,
 *      content: string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: ArticleRepository::class, baseUrl: 'blog/article')]
class Article implements ModelInterface
{
    /**
     * @param LazyValueInterface<?File> $logo
     */
    public function __construct(
        protected string $id,
        protected string $title,
        protected string $content,
        protected string $slug,
        protected LazyValueInterface $logo,
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

    public function getLogo(): ?File
    {
        return $this->logo->getValue();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'slug' => $this->getSlug(),
            'content' => $this->getContent(),
        ];
    }
}
