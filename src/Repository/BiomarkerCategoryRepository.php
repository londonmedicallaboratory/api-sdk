<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\File\FileInterface;
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
        return new BiomarkerCategory(
            id          : $entity['id'],
            name        : $entity['name'],
            slug        : $entity['slug'],
            nrOfProducts: new ResolvedValue($entity['nr_of_products'] ?? null),
            description : $entity['description'],
            logo        : new LazyPromise($this->getLogo($entity['id'])),
        );
    }

    protected function getBaseUrl(): string
    {
        return '/biomarker_categories';
    }

    /**
     * @return PromiseInterface<?FileInterface>
     */
    private function getLogo(string $id): PromiseInterface
    {
        $url = sprintf('/biomarker_categories/%s/logo', $id);

        return $this->get(FileRepository::class)->findOneByUrl(url: $url);
    }
}
