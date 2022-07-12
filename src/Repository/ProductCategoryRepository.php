<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\View\Lazy\ResolvedValue;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Category\ProductCategory;
use LML\SDK\Entity\Category\CategoryInterface;

/**
 * @psalm-import-type S from CategoryInterface
 *
 * @extends AbstractRepository<S, CategoryInterface, array{product_id?: string}>
 *
 * @see CategoryInterface
 */
class ProductCategoryRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): ProductCategory
    {
        return new ProductCategory(
            id          : $entity['id'],
            name        : $entity['name'],
            slug        : $entity['slug'],
            nrOfProducts: new ResolvedValue($entity['nr_of_products'] ?? null),
            description : $entity['description'],
            logo        : new ResolvedValue(null),
        );
    }

    protected function getBaseUrl(): string
    {
        return '/product_categories';
    }
}
