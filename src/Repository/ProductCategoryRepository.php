<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\File\File;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Category\Category;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Category\ProductCategory;

/**
 * @psalm-import-type S from Category
 *
 * @extends AbstractRepository<S, Category, array{
 *     product_id?: string,
 *     slug_in?: list<string>
 * }>
 */
class ProductCategoryRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): ProductCategory
    {
        $id = $entity['id'];
        $logoId = $entity['logo_id'] ?? null;
        $iconId = $entity['icon_id'] ?? null;

        return new ProductCategory(
            id: $id,
            name: $entity['name'],
            slug: $entity['slug'],
            nrOfProducts: new ResolvedValue($entity['nr_of_products'] ?? null),
            description: $entity['description'],
            logo: new LazyPromise($this->getLogo($logoId, $id)),
            icon: new LazyPromise($this->getIcon($iconId, $id)),
            color: new ResolvedValue($entity['color'] ?? null),
        );
    }

    /**
     * @return PromiseInterface<?File>
     */
    private function getLogo(?string $logoId, string $id): PromiseInterface
    {
        $fileRepository = $this->get(FileRepository::class);
        // there is something wrong with psalm and unions when using resolve(); wait for clue/react stubs to be created
        //        if (!$logoId) {
        //            return resolve(null);
        //        }

        if (!$logoId) {
            return $fileRepository->find($logoId); // this will always return null, but at least psalm is happy, and we save on API request
        }

        $url = sprintf('/product_categories/%s/logo', $id);

        return $fileRepository->find(url: $url);
    }

    /**
     * @return PromiseInterface<?File>
     */
    private function getIcon(?string $iconId, string $id): PromiseInterface
    {
        $fileRepository = $this->get(FileRepository::class);

        if (!$iconId) {
            return $fileRepository->find($iconId);
        }

        $url = sprintf('/product_categories/%s/icon', $id);

        return $fileRepository->find(url: $url);
    }
}
