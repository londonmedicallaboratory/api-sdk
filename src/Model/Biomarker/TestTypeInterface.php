<?php

declare(strict_types=1);

namespace App\Model\Biomarker;

interface TestTypeInterface
{
    public function getType(): string;

    public function getName(): string;
}
