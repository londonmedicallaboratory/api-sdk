<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestRegistration;

use LML\SDK\Model\ModelInterface;

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
