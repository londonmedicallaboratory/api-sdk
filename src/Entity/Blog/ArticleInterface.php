<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Blog;

use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\File\FileInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      title: string,
 *      slug: string,
 *      content: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface ArticleInterface extends ModelInterface
{
    public function getTitle(): string;

    public function getSlug(): string;

    public function getContent(): string;

    public function getLogo(): ?FileInterface;
}
