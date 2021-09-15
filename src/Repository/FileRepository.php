<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Model\File\File;
use LML\SDK\Model\File\FileInterface;
use LML\SDK\Service\Model\AbstractRepository;

/**
 * @psalm-import-type S from FileInterface
 *
 * @extends AbstractRepository<S, FileInterface, array{product_id?: string}>
 *
 * @see FileInterface
 */
class FileRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer)
    {
        $id = $entity['id'];

        return new File(
            id: $id,
            filename: $entity['filename'],
            url: $entity['url'],
            isPrimary: $entity['is_primary'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/files';
    }
}
