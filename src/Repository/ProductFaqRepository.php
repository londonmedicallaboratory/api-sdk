<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Model\Product\ProductFaq;
use LML\SDK\Model\Product\ProductFaqInterface;
use LML\SDK\Service\Model\AbstractRepository;

/**
 * @psalm-import-type S from ProductFaqInterface
 * @extends AbstractRepository<S, ProductFaqInterface, array>
 *
 * @see ProductFaqInterface
 */
class ProductFaqRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): ProductFaqInterface
    {
        return new ProductFaq(
            id: $entity['id'],
            question: $entity['question'],
            answer: $entity['answer'],
            priority: $entity['priority'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/product_faq';
    }
}
