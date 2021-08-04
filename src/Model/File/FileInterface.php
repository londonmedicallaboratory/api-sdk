<?php

declare(strict_types=1);

namespace LML\SDK\Model\File;

use LML\SDK\Model\IdInterface;

interface FileInterface extends IdInterface
{
    public function getFilename(): string;

    public function isPrimary(): bool;

    public function getUrl(): string;
}
