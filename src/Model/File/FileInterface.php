<?php

declare(strict_types=1);

namespace LML\SDK\Model\File;

use LML\SDK\Model\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      filename: string,
 *      url: string,
 *      is_primary: bool,
 * }
 *
 * @extends ModelInterface<S>
 */
interface FileInterface extends ModelInterface
{
    public function getFilename(): string;

    public function isPrimary(): bool;

    public function getUrl(): string;
}
