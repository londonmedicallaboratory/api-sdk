<?php

declare(strict_types=1);

namespace LML\SDK\Entity\PSA;

use Stringable;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Repository\PSARepository;

/**
 * @psalm-type S=array{
 *     id: string,
 *     message: string,
 *     type: ?string,
 *     link: ?string,
 *     background_color: ?string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: PSARepository::class, baseUrl: 'psa')]
class PSA implements ModelInterface, Stringable
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

    public function __toString(): string
    {
        return $this->message;
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

    public function toArray(): array
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
