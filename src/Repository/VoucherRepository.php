<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\Voucher\Voucher;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from Voucher
 *
 * @extends AbstractRepository<S, Voucher, array{
 *     code?: string,
 * }>
 */
class VoucherRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Voucher
    {
        return new Voucher(
            id: $entity['id'],
            type: new ResolvedValue($entity['type']),
            value: new ResolvedValue($entity['value']),
            code: new ResolvedValue($entity['code']),
            promotionName: new ResolvedValue($entity['promotion_name']),
        );
    }
}
