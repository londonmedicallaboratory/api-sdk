<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use LML\SDK\Entity\ModelInterface;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Biomarker\Biomarker;

/**
 * @template TBiomarker of Biomarker
 *
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
 *      out_of_range?: null|'high'|'low',
 * }
 *
 * @implements ModelInterface<S>
 */
class LabResult implements ModelInterface
{
    /**
     * @param LazyValueInterface<TBiomarker> $biomarker
     * @param LazyValueInterface<string> $name
     * @param LazyValueInterface<string> $code
     */
    public function __construct(
        protected string $id,
        protected LazyValueInterface $biomarker,
        protected LazyValueInterface $name,
        protected LazyValueInterface $code,
        protected null|bool|string $value,
        private bool $isSuccessful,
        protected ?string $minRange,
        protected ?string $maxRange,
        protected ?string $comment,
    )
    {
    }

    /**
     * @return TBiomarker
     */
    public function getBiomarker(): Biomarker
    {
        return $this->biomarker->getValue();
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
        return $this->name->getValue();
    }

    public function getValue(): null|bool|string
    {
        return $this->value;
    }

    public function getCode(): string
    {
        return $this->code->getValue();
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
