<?php

declare(strict_types=1);

namespace LML\SDK\Model\Page;

use LML\SDK\Model\ModelInterface;
use LML\SDK\Model\SluggableInterface;

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