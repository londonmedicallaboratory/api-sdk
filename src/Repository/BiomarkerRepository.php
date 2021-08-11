<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\View\Lazy\LazyValue;
use LML\SDK\Lazy\LazyPromise;
use React\Promise\PromiseInterface;
use LML\SDK\Model\Biomarker\Biomarker;
use LML\SDK\Model\Category\CategoryInterface;
use LML\SDK\ViewFactory\AbstractViewRepository;
use LML\SDK\Model\Biomarker\BiomarkerInterface;
use function sprintf;

/**
 * @psalm-import-type S from BiomarkerInterface
 *
 * @extends AbstractViewRepository<S, BiomarkerInterface, array{product_id?: string}>
 *
 * @see BiomarkerInterface
 */
class BiomarkerRepository extends AbstractViewRepository
{
    protected function one($entity, $options, LazyValue $optimizer)
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
    private function getCategory(string $id)
    {
        $url = sprintf('/biomarker/%s/category', $id);

        return $this->get(CategoryRepository::class)->findOneByOrException(url: $url);
    }
}
