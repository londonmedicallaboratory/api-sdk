<?php

declare(strict_types=1);

namespace App\Model\Biomarker;

use App\Model\IdInterface;
use App\Model\Category\CategoryInterface;

interface BiomarkerInterface extends IdInterface
{
    public function getName(): string;

    public function getSlug(): string;

    public function getCategory(): CategoryInterface;

    /**
     * @return iterable<TestTypeInterface>
     */
    public function getTestTypes(): iterable;
}
