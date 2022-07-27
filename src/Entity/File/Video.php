<?php

declare(strict_types=1);

namespace LML\SDK\Entity\File;

use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\VideoRepository;

/**
 * @see VideoRepository::one()
 */
class Video implements VideoInterface
{
    /**
     * @param LazyValueInterface<string> $embedHtml
     * @param LazyValueInterface<string> $previewImageUrl
     */
    public function __construct(
        protected string             $id,
        protected LazyValueInterface $embedHtml,
        protected LazyValueInterface $previewImageUrl,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmbedHtml(): string
    {
        return $this->embedHtml->getValue();
    }

    public function getPreviewImageUrl(): string
    {
        return $this->previewImageUrl->getValue();
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->getId(),
            'embed_html'        => $this->getEmbedHtml(),
            'preview_image_url' => $this->getPreviewImageUrl(),
        ];
    }
}
