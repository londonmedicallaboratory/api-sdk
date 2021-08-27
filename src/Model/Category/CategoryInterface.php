<?php

declare(strict_types=1);

namespace LML\SDK\Model\Category;

use LML\SDK\Model\ModelInterface;
use LML\SDK\Model\SluggableInterface;
use LML\SDK\Model\Biomarker\BiomarkerInterface;

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
