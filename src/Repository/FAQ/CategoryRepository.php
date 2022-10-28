<?php

declare(strict_types=1);

namespace LML\SDK\Repository\FAQ;

use LML\SDK\Entity\FAQ\Category;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\FAQ\CategoryTypeEnum;
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
        return new Category(
            id: $entity['id'] ?? null,
            name: new ResolvedValue($entity['name']),
            type: new ResolvedValue(CategoryTypeEnum::from($entity['type'])),
        );
    }
}
