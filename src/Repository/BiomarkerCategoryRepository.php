<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\View\Lazy\ResolvedValue;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Category\CategoryInterface;
use LML\SDK\Entity\Category\BiomarkerCategory;

/**
 * @psalm-import-type S from CategoryInterface
 *
 * @extends AbstractRepository<S, CategoryInterface, array{product_id?: string}>
 */
class BiomarkerCategoryRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): BiomarkerCategory
    {
        $logoData = $entity['logo'] ?? null;
        $logo = $logoData ? $this->get(FileRepository::class)->buildOne($logoData) : null;
        
        return new BiomarkerCategory(
            id          : $entity['id'],
            name        : $entity['name'],
            slug        : $entity['slug'],
            nrOfProducts: new ResolvedValue($entity['nr_of_products'] ?? null),
            description : $entity['description'],
            logo        : new ResolvedValue($logo),
        );
    }

    protected function getBaseUrl(): string
    {
        return '/biomarker_categories';
    }
}
