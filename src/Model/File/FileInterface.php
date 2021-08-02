<?php

declare(strict_types=1);

namespace App\Model\File;

use App\Model\IdInterface;

interface FileInterface extends IdInterface
{
    public function getFilename(): string;

    public function isPrimary(): bool;

    public function getUrl(): string;
}
