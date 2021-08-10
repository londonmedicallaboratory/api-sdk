<?php

declare(strict_types=1);

namespace LML\SDK\Model\Biomarker;

use LML\SDK\Model\ModelInterface;
use LML\SDK\Model\Category\CategoryInterface;

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
interface BiomarkerInterface extends ModelInterface
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
