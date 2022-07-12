<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Category;

use Stringable;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\SluggableInterface;
use LML\SDK\Entity\File\FileInterface;
use LML\SDK\Entity\Biomarker\BiomarkerInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      nr_of_products?: int,
 *      slug: string,
 *      description: ?string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface CategoryInterface extends ModelInterface, SluggableInterface, Stringable
{
    public function getName(): string;

    public function getSlug(): string;

    public function getDescription(): ?string;

    /**
     * @return list<BiomarkerInterface>
     */
    public function getBiomarkers();

    public function getNrOfProducts(): ?int;

    public function getLogo(): ?FileInterface;
}
