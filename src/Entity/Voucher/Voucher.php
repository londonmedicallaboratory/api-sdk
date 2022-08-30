<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Voucher;

use LML\SDK\Attribute\Entity;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\VoucherRepository;

/**
 * @psalm-import-type TType from VoucherInterface
 */
#[Entity(repositoryClass: VoucherRepository::class, baseUrl: 'voucher')]
class Voucher implements VoucherInterface
{
    /**
     * @param LazyValueInterface<TType> $type
     * @param LazyValueInterface<float> $value
     * @param LazyValueInterface<string> $code
     * @param LazyValueInterface<string> $promotionName
     */
    public function __construct(
        protected string           $id,
        private LazyValueInterface $type,
        private LazyValueInterface $value,
        private LazyValueInterface $code,
        private LazyValueInterface $promotionName,
    )
    {
    }

    public function __toString(): string
    {
        $value = $this->getValue();
        $type = $this->getType();

        return match ($type) {
            'percent' => sprintf('%d%%', $value),
            'amount' => sprintf('£ %s', number_format($value, 2)),
        };
    }

    public function getValue(): float
    {
        return $this->value->getValue();
    }

    /**
     * @return TType
     */
    public function getType(): string
    {
        return $this->type->getValue();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code->getValue();
    }

    public function getPromotionName(): string
    {
        return $this->promotionName->getValue();
    }

    public function toArray()
    {
        return [
            'id'             => $this->getId(),
            'type'           => $this->getType(),
            'value'          => $this->getValue(),
            'code'           => $this->getCode(),
            'promotion_name' => $this->getPromotionName(),
        ];
    }
}
