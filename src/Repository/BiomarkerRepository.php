<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Category\Category;
use LML\SDK\Entity\Biomarker\Biomarker;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from Biomarker
 *
 * @extends AbstractRepository<S, Biomarker, array{product_id?: string}>
 */
class BiomarkerRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Biomarker
    {
        $id = $entity['id'];

        return new Biomarker(
            id: $id,
            name: $entity['name'],
            slug: $entity['slug'],
            code: $entity['code'],
            description: $entity['description'],
//            category: new LazyPromise($this->getCategory($id)),
            category: new LazyPromise($this->getCategory($entity['category_id'])),
        );
    }

    /**
     * @return PromiseInterface<Category>
     */
    private function getCategory(string $id): PromiseInterface
    {
        return $this->get(BiomarkerCategoryRepository::class)->fetch($id);
    }
}
