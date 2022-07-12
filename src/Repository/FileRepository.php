<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Entity\File\File;
use LML\SDK\Entity\File\FileInterface;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from FileInterface
 *
 * @extends AbstractRepository<S, FileInterface, array{product_id?: string}>
 *
 * @see FileInterface
 */
class FileRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): File
    {
        $id = $entity['id'];

        return new File(
            id       : $id,
            filename : $entity['filename'],
            url      : $entity['url'],
            isPrimary: $entity['is_primary'] ?? null,
        );
    }

    protected function getBaseUrl(): string
    {
        return '/files';
    }
}
