<?php

declare(strict_types=1);

namespace LML\SDK\Entity\PSA;

use LML\SDK\Attribute\Entity;
use LML\SDK\Repository\PSARepository;

#[Entity(repositoryClass: PSARepository::class, baseUrl: 'psa')]
class PSA implements PSAInterface
{
    public function __construct(
        protected string $id,
        protected string $message,
        protected ?string $type,
        protected ?string $link,
        protected ?string $backgroundColor,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'message' => $this->getMessage(),
            'type' => $this->getType(),
            'link' => $this->getLink(),
            'background_color' => $this->getBackgroundColor(),
        ];
    }
}
