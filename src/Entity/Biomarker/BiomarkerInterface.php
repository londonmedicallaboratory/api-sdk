<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Biomarker;

use Stringable;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\SluggableInterface;
use LML\SDK\Entity\Category\CategoryInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      code: string,
 *      slug: string,
 *      description: ?string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface BiomarkerInterface extends ModelInterface, SluggableInterface, Stringable
{
    public function getName(): string;

    public function getSlug(): string;

    public function getCode(): string;

    public function getDescription(): ?string;

    public function getCategory(): CategoryInterface;

    /**
     * @return list<TestTypeInterface>
     */
    public function getTestTypes();
}
