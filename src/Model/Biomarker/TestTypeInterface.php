<?php

declare(strict_types=1);

namespace LML\SDK\Model\Biomarker;

interface TestTypeInterface
{
    public function getType(): string;

    public function getName(): string;
}
