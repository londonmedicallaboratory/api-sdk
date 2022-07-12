<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Category;

use LML\SDK\Attribute\Entity;
use LML\SDK\Repository\BiomarkerCategoryRepository;

#[Entity(repositoryClass: BiomarkerCategoryRepository::class, baseUrl: 'biomarker_categories')]
class BiomarkerCategory extends Category
{
}
