<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use React\Promise\PromiseInterface;
use LML\SDK\Model\Biomarker\Biomarker;
use LML\SDK\Model\Category\CategoryInterface;
use LML\SDK\Service\Model\AbstractRepository;
use LML\SDK\Model\Biomarker\BiomarkerInterface;
use function sprintf;

/**
 * @psalm-import-type S from BiomarkerInterface
 *
 * @extends AbstractRepository<S, BiomarkerInterface, array{product_id?: string}>
 *
 * @see BiomarkerInterface
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
            category: new LazyPromise($this->getCategory($id))
        );
    }

    protected function getBaseUrl(): string
    {
        return '/biomarkers';
    }

    /**
     * @return PromiseInterface<CategoryInterface>
     */
    private function getCategory(string $id): PromiseInterface
    {
        $url = sprintf('/biomarker/%s/category', $id);

        return $this->get(BiomarkerCategoryRepository::class)->fetchOneBy(url: $url);
    }
}
