<?php
declare(strict_types=1);

namespace LML\SDK\Entity\ProductCollection;

use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      slug: string,
 *      description: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface ProductCollectionInterface extends ModelInterface
{
    public function getName(): string;

    public function getSlug(): string;

    public function getDescription(): string;
}
