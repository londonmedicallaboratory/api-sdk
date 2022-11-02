<?php

declare(strict_types=1);

namespace LML\SDK\Repository\FAQ;

use LML\SDK\Entity\FAQ\FAQ;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\FAQ\Category;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\FAQ\CategoryTypeEnum;
use LML\SDK\Service\API\AbstractRepository;
use function React\Promise\resolve;

/**
 * @psalm-import-type S from Category
 *
 * @extends AbstractRepository<S, Category, array>
 */
class CategoryRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Category
    {
        $id = $entity['id'] ?? null;

        return new Category(
            id: $id,
            name: new ResolvedValue($entity['name']),
            type: new ResolvedValue(CategoryTypeEnum::from($entity['type'])),
            faqs: new LazyPromise($this->getFaqs($id))
        );
    }


    /**
     * @return PromiseInterface<list<FAQ>>
     */
    private function getFaqs(?string $id): PromiseInterface
    {
        if (!$id) {
            return resolve([]);
        }

        $url = sprintf('/faq/category/%s/faqs', $id);

        return $this->get(FAQRepository::class)->findBy(url: $url);
    }
}
