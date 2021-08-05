<?php

declare(strict_types=1);

namespace LML\SDK\Repository\Product;

use LML\SDK\Model\Product\Product;
use LML\SDK\Factory\ProductMapper;
use LML\SDK\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<Product, ProductMapper>
 */
class ProductRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(Product::class, ProductMapper::class);
    }

    protected function getBaseUrl(): string
    {
        return '/product';
    }
}
