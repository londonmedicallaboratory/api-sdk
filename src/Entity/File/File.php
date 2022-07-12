<?php

declare(strict_types=1);

namespace LML\SDK\Entity\File;

class File implements FileInterface
{
    public function __construct(
        protected string $id,
        protected string $filename,
        protected string $url,
        protected ?bool  $isPrimary = null,
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
        ];
        if (null !== $isPrimary) {
            $data['is_primary'] = $isPrimary;
        }

        return $data;
    }
}
