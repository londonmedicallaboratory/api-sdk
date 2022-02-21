<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      value: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface LabResultInterface extends ModelInterface
{
    public function getName(): string;

    public function getValue(): string;
}
