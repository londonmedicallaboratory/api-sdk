<?php

declare(strict_types=1);

namespace LML\SDK\Model\Category;

use LML\SDK\Model\IdInterface;
use LML\SDK\Model\Biomarker\BiomarkerInterface;

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
