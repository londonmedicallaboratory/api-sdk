<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Voucher;

use Stringable;
use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type TType = 'percent'|'amount'
 *
 * @psalm-type S=array{
 *     id: string,
 *     type: TType,
 *     value: float,
 *     code: string,
 *     promotion_name: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface VoucherInterface extends ModelInterface, Stringable
{
    public function getValue(): float;

    /**
     * @return TType
     */
    public function getType(): string;

    public function getCode(): string;

    public function getPromotionName(): string;
}
