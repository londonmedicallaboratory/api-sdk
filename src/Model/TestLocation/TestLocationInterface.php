<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestLocation;

use LML\SDK\Model\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      full_address: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface TestLocationInterface extends ModelInterface
{
}
