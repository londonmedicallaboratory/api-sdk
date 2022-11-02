<?php

declare(strict_types=1);

namespace LML\SDK\Repository\FAQ;

use LML\SDK\Entity\FAQ\FAQ;
use LML\View\Lazy\LazyValue;
use LML\SDK\Entity\FAQ\Category;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Exception\GhostEntityException;

/**
 * @psalm-import-type S from FAQ
 *
 * @extends AbstractRepository<S, FAQ, array{
 *     category?: string,
 *     category_type?: string,
 * }>
 */
class FAQRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): FAQ
    {
        return new FAQ(
            id: $entity['id'] ?? null,
            category: new LazyValue(fn() => $this->getGhostCategory($entity['category_id'])),
            question: new ResolvedValue($entity['question']),
            answer: new ResolvedValue($entity['answer']),
        );
    }

    /**
     * Avoid unnecessary calls to API; identity map is still not fully on par with one in Doctrine.
     *
     * By allowing only ID field, objects can still be used in grouping.
     *
     * DO NOT typehint `never` on closures, a bug in PHP will break compile process: @see https://github.com/php/php-src/issues/7900
     */
    private function getGhostCategory(string $id): Category
    {
        return new Category(
            id: $id,
            type: new LazyValue(fn() => throw new GhostEntityException()),
            name: new LazyValue(fn() => throw new GhostEntityException()),
            faqs: new LazyValue(fn() => throw new GhostEntityException()),
        );
    }
}
