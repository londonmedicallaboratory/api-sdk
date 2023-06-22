<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Entity\Page\Page;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from Page
 *
 * @extends AbstractRepository<S, Page, array>
 */
class PageRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Page
    {
        $id = $entity['id'];

        return new Page(
            id: $id,
            name: $entity['name'],
            slug: $entity['slug'],
            content: $entity['content'],
        );
    }
}
