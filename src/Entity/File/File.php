<?php

declare(strict_types=1);

namespace LML\SDK\Entity\File;

use LML\SDK\Attribute\Entity;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\FileRepository;

#[Entity(repositoryClass: FileRepository::class, baseUrl: 'file')]
class File implements FileInterface
{
    /**
     * @param LazyValueInterface<array<string, string>> $thumbnails
     */
    public function __construct(
        protected string             $id,
        protected string             $filename,
        protected string             $url,
        protected ?bool              $isPrimary,
        protected LazyValueInterface $thumbnails,
    )
    {
    }

    public function getThumbnails(): array
    {
        return $this->thumbnails->getValue();
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isPrimary(): ?bool
    {
        return $this->isPrimary;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        $isPrimary = $this->isPrimary();

        $data = [
            'id'         => $this->getId(),
            'filename'   => $this->getFilename(),
            'url'        => $this->getUrl(),
            'is_primary' => $isPrimary,
            'thumbnails' => $this->getThumbnails(),
        ];
        if (null !== $isPrimary) {
            $data['is_primary'] = $isPrimary;
        }

        return $data;
    }
}
