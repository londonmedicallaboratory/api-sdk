<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\Category\Category;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Category\CategoryInterface;

/**
 * @psalm-import-type S from CategoryInterface
 *
 * @extends AbstractRepository<S, CategoryInterface, array{product_id?: string}>
 */
class BiomarkerCategoryRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Category
    {
        return new Category(
            id          : $entity['id'],
            name        : $entity['name'],
            slug        : $entity['slug'],
            nrOfProducts: new ResolvedValue($entity['nr_of_products'] ?? null),
            description : $entity['description'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/biomarker_categories';
    }
}
