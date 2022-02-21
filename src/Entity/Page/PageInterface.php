<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Page;

use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\SluggableInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      slug: string,
 *      content: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface PageInterface extends ModelInterface, SluggableInterface
{
    public function getName(): string;

    public function getSlug(): string;

    public function getContent(): string;
}
