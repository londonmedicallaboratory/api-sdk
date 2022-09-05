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

    public function toArray()
    {
        return [
            'id'      => $this->getId(),
            'message' => $this->getMessage(),
        ];
    }
}
