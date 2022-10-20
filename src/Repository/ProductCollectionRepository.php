<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\File\FileInterface;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\ProductCollection\ProductCollection;
use LML\SDK\Entity\ProductCollection\ProductCollectionInterface;

/**
 * @psalm-import-type S from ProductCollectionInterface
 *
 * @extends AbstractRepository<S, ProductCollection, array>
 */
class ProductCollectionRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): ProductCollection
    {
        $id = $entity['id'];

        return new ProductCollection(
            id: $id,
            name: $entity['name'],
            slug: $entity['slug'],
            description: $entity['description'],
            logo: new LazyPromise($this->getLogo($id)),
        );
    }

    /**
     * @return PromiseInterface<?FileInterface>
     */
    private function getLogo(string $id): PromiseInterface
    {
        $url = sprintf('/product_collection/%s/logo', $id);

        return $this->get(FileRepository::class)->findOneBy(url: $url);
    }
}
