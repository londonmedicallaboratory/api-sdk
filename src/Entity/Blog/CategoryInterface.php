<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Blog;

use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      slug: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface CategoryInterface extends ModelInterface
{
    public function getName(): string;

    public function getSlug(): string;
}
