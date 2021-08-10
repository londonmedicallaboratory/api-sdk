<?php

declare(strict_types=1);

namespace LML\SDK\Model\File;

class File implements FileInterface
{
    public function __construct(
        private string $id,
        private string $filename,
        private string $url,
        private bool $isPrimary,
    )
    {
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray()
    {
        return [
            'id'         => $this->getId(),
            'filename'   => $this->getFilename(),
            'url'        => $this->getUrl(),
            'is_primary' => $this->isPrimary(),
        ];
    }
}
