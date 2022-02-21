<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Entity\Category\Category;
use LML\SDK\Entity\Category\CategoryInterface;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from CategoryInterface
 *
 * @extends AbstractRepository<S, CategoryInterface, array{product_id?: string}>
 *
 * @see CategoryInterface
 */
class ProductCategoryRepository extends AbstractRepository
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
        return '/product_categories';
    }
}
