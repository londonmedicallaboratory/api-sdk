<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\File\FileInterface;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Category\ProductCategory;
use LML\SDK\Entity\Category\CategoryInterface;

/**
 * @psalm-import-type S from CategoryInterface
 *
 * @extends AbstractRepository<S, CategoryInterface, array{
 *     product_id?: string,
 * }>
 */
class ProductCategoryRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): ProductCategory
    {
        $id = $entity['id'];

        return new ProductCategory(
            id          : $id,
            name        : $entity['name'],
            slug        : $entity['slug'],
            nrOfProducts: new ResolvedValue($entity['nr_of_products'] ?? null),
            description : $entity['description'],
            logo        : new LazyPromise($this->getLogo($id)),
        );
    }

    protected function getBaseUrl(): string
    {
        return '/product_categories';
    }

    /**
     * @return PromiseInterface<?FileInterface>
     */
    private function getLogo(string $id): PromiseInterface
    {
        $url = sprintf('/product_categories/%s/logo', $id);

        return $this->get(FileRepository::class)->findOneByUrl(url: $url);
    }
}
