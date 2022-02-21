<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Category;

use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\SluggableInterface;
use LML\SDK\Entity\Biomarker\BiomarkerInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      slug: string,
 *      description: ?string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface CategoryInterface extends ModelInterface, SluggableInterface
{
    public function getName(): string;

    public function getSlug(): string;

    public function getDescription(): ?string;

    /**
     * @return list<BiomarkerInterface>
     */
    public function getBiomarkers();
}
