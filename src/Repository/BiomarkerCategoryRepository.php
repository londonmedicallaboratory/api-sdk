<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Model\Category\Category;
use LML\SDK\Model\Category\CategoryInterface;
use LML\SDK\ViewFactory\AbstractViewRepository;

/**
 * @psalm-import-type S from CategoryInterface
 *
 * @extends AbstractViewRepository<S, CategoryInterface, array{product_id?: string}>
 *
 * @see CategoryInterface
 */
class BiomarkerCategoryRepository extends AbstractViewRepository
{
    protected function one($entity, $options, $optimizer): Category
    {
        return new Category(
            id: $entity['id'],
            name: $entity['name'],
            slug: $entity['slug'],
            description: $entity['description'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/biomarker_categories';
    }
}
