<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Entity\Product\ProductFaq;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Product\ProductFaqInterface;

/**
 * @psalm-import-type S from ProductFaqInterface
 * @extends AbstractRepository<S, ProductFaq, array>
 */
class ProductFaqRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): ProductFaq
    {
        return new ProductFaq(
            id      : $entity['id'],
            question: $entity['question'],
            answer  : $entity['answer'],
            priority: $entity['priority'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/product_faq';
    }
}
