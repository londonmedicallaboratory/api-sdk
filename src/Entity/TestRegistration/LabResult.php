<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      code: string,
 *      value: null|bool|string,
 *      min_range: ?string,
 *      max_range: ?string,
 *      comment?: ?string,
 *      successful: bool,
 *      error_reason?: string,
 * }
 *
 * @implements ModelInterface<S>
 */
class LabResult implements ModelInterface
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $code,
        protected null|bool|string $value,
        private bool $isSuccessful,
        protected ?string $minRange,
        protected ?string $maxRange,
        protected ?string $comment,
    )
    {
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    public function getMinRange(): ?string
    {
        return $this->minRange;
    }

    public function getMaxRange(): ?string
    {
        return $this->maxRange;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): null|bool|string
    {
        return $this->value;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'code' => $this->getCode(),
            'value' => $this->value,
            'min_range' => $this->getMinRange(),
            'max_range' => $this->getMaxRange(),
            'comment' => $this->comment,
            'successful' => $this->isSuccessful,
        ];
    }
}
