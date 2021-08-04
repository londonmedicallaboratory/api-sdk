<?php

declare(strict_types=1);

namespace LML\SDK\Model\Biomarker;

use LML\SDK\Model\IdInterface;
use LML\SDK\Model\Category\CategoryInterface;

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
