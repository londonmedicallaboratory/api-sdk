<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Category;

use LML\SDK\Attribute\Entity;
use LML\SDK\Repository\ProductCategoryRepository;

#[Entity(repositoryClass: ProductCategoryRepository::class, baseUrl: 'product_categories')]
class ProductCategory extends Category
{
}
