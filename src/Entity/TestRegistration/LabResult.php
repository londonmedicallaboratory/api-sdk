<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Biomarker\Biomarker;
use LML\SDK\Repository\LabResultRepository;

/**
 * @template TBiomarker of Biomarker
 *
 * @psalm-type S=array{
 *      id: string,
 *      biomarker_id?: string,
 *      name: string,
 *      code: string,
 *      value: null|bool|string,
 *      min_range: ?string,
 *      max_range: ?string,
 *      comment?: ?string,
 *      successful: bool,
 *      error_reason?: string,
 *      out_of_range?: null|'high'|'low',
 *      biomarker_id: string,
 *      unit_type?: ?string,
 *      human_readable_value?: ?string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: LabResultRepository::class, baseUrl: 'lab_result')]
class LabResult implements ModelInterface
{
    public ?string $unitName = null;
    public ?string $errorReason = null;
    private ?string $humanReadableValue = null;

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

    public function getResultLowOrHighMarker(): ?string
    {
        return match ($this->getOutOfRangeValue()) {
            'high' => 'High',
            'low' => 'Low',
            default => 'Normal',
        };
    }

    public function getFriendlyRange(): string
    {
        if ($this->errorReason) {
            return '';
        }

        $minRange = $this->getMinRange();
        $maxRange = $this->getMaxRange();

        return match (true) {
            !$minRange && !$maxRange => '',
            !$minRange => sprintf('< %s', $maxRange),
            !$maxRange => sprintf('> %s', $minRange),
            default => sprintf('%s - %s', $minRange, $maxRange),
        };
    }

    public function isTooHigh(): bool
    {
        $max = $this->getMaxRange();
        if (!is_numeric($max)) {
            return false;
        }

        $value = $this->getValue();
        if (is_string($value) && str_starts_with($value, '>')) {
            $value = trim($value, '> ');
        }

        return (float)$value > (float)$max;
    }

    public function isTooLow(): bool
    {
        $minRange = $this->getMinRange();
        if (!is_numeric($minRange)) {
            return false;
        }

        $value = $this->getValue();
        if (is_string($value) && str_starts_with($value, '<')) {
            $value = trim($value, '< ');
        }
        // there are some other weird characters here; bail
        if (!is_numeric($value)) {
            return false;
        }

        return (float)$value < (float)$minRange;
    }

    public function isOutsideOfRange(): bool
    {
        return !$this->isWithinRange();
    }

    public function isWithinRange(): bool
    {
        return !$this->isTooHigh() && !$this->isTooLow();
    }

    public function getHumanReadableValue(): ?string
    {
        return $this->humanReadableValue;
    }

    public function setHumanReadableValue(?string $humanReadableValue): void
    {
        $this->humanReadableValue = $humanReadableValue;
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
            'biomarker_id' => $this->getBiomarker()->getId(),
        ];
    }

    /**
     * @return null|'high'|'low'
     */
    protected function getOutOfRangeValue(): ?string
    {
        if ($this->errorReason) {
            return null;
        }

        if ($this->isTooHigh()) {
            return 'high';
        }
        if ($this->isTooLow()) {
            return 'low';
        }

        return null;
    }
}
