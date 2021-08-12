<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Model\Product\ProductFaq;
use LML\SDK\Model\Product\ProductFaqInterface;
use LML\SDK\ViewFactory\AbstractViewRepository;

/**
 * @psalm-import-type S from ProductFaqInterface
 * @extends AbstractViewRepository<S, ProductFaqInterface, array>
 *
 * @see ProductFaqInterface
 */
class ProductFaqRepository extends AbstractViewRepository
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
