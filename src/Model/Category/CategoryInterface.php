<?php

declare(strict_types=1);

namespace App\Model\Category;

use App\Model\IdInterface;
use App\Model\Biomarker\BiomarkerInterface;

interface CategoryInterface extends IdInterface
{
    public function getName(): string;

    public function getSlug(): string;

    public function getDescription(): ?string;

    /**
     * @return iterable<BiomarkerInterface>
     */
    public function getBiomarkers(): iterable;
}
