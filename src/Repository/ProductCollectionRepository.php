<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\File\File;
use React\Promise\PromiseInterface;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\ProductCollection\ProductCollection;

/**
 * @psalm-import-type S from ProductCollection
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
            headerImage: new LazyPromise($this->getHeaderImage($id)),
        );
    }

    /**
     * @return PromiseInterface<?File>
     */
    private function getHeaderImage(string $id): PromiseInterface
    {
        $url = sprintf('/product_collection/%s/header_image', $id);

        return $this->get(FileRepository::class)->findOneBy(url: $url);
    }
}
