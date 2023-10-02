<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\File\File;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Category\Category;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Category\BiomarkerCategory;

/**
 * @psalm-import-type S from Category
 *
 * @extends AbstractRepository<S, Category, array{product_id?: string}>
 */
class BiomarkerCategoryRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): BiomarkerCategory
    {
        return new BiomarkerCategory(
            id: $entity['id'],
            name: $entity['name'],
            slug: $entity['slug'],
            nrOfProducts: new ResolvedValue($entity['nr_of_products'] ?? null),
            description: $entity['description'],
            logo: new LazyPromise($this->getLogo($entity['id'])),
        );
    }

    /**
     * @return PromiseInterface<?File>
     */
    private function getLogo(string $id): PromiseInterface
    {
        $url = sprintf('/biomarker_categories/%s/logo', $id);

        return $this->get(FileRepository::class)->find(url: $url);
    }
}
