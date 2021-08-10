<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\View\Lazy\LazyValue;
use LML\SDK\Model\File\File;
use LML\SDK\Model\File\FileInterface;
use LML\SDK\ViewFactory\AbstractViewRepository;

/**
 * @psalm-import-type S from FileInterface
 *
 * @extends AbstractViewRepository<S, FileInterface, array{product_id?: string}>
 *
 * @see FileInterface
 */
class FileRepository extends AbstractViewRepository
{
    protected function one($entity, $options, LazyValue $optimizer)
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
