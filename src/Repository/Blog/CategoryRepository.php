<?php

declare(strict_types=1);

namespace LML\SDK\Repository\Blog;

use LML\SDK\Entity\Blog\Category;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from Category
 *
 * @extends AbstractRepository<S, Category, array>
 */
class CategoryRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Category
    {
        $id = $entity['id'];

        return new Category(
            id: $id,
            name: $entity['name'],
            slug: $entity['slug'],
        );
    }
}
