<?php

declare(strict_types=1);

namespace LML\SDK\Entity\File;

use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *     id: string,
 *     filename: string,
 *     url: string,
 *     is_primary?: ?bool,
 *     thumbnails?: array<string, string>,
 * }
 *
 * @extends ModelInterface<S>
 */
interface FileInterface extends ModelInterface
{
    /**
     * @return array<string, string>
     */
    public function getThumbnails(): array;

    public function getFilename(): string;

    public function isPrimary(): ?bool;

    public function getUrl(): string;
}
