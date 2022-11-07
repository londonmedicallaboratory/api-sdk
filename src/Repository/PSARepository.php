<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Entity\PSA\PSA;
use LML\SDK\Entity\PSA\PSAInterface;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from PSAInterface
 *
 * @extends AbstractRepository<S, PSA, array{
 *     type?: null|'product',
 * }>
 */
class PSARepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): PSA
    {
        $id = $entity['id'];

        return new PSA(
            id: $id,
            message: $entity['message'],
            type: $entity['type'],
            link: $entity['link'],
            backgroundColor: $entity['background_color'],
        );
    }
}
