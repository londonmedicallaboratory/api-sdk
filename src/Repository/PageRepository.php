<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Model\Page\Page;
use LML\SDK\Model\Page\PageInterface;
use LML\SDK\ViewFactory\AbstractViewRepository;

/**
 * @psalm-import-type S from PageInterface
 *
 * @extends AbstractViewRepository<S, PageInterface, array>
 *
 * @see PageInterface
 */
class PageRepository extends AbstractViewRepository
{
    protected function one($entity, $options, $optimizer)
    {
        $id = $entity['id'];

        return new Page(
            id: $id,
            name: $entity['name'],
            slug: $entity['slug'],
            content: $entity['content'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/page';
    }

    protected function getCacheTimeout(): ?int
    {
        return 500;
    }
}
