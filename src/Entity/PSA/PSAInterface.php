<?php

declare(strict_types=1);

namespace LML\SDK\Entity\PSA;

use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      message: string,
 *      type: ?string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface PSAInterface extends ModelInterface
{
    public function getMessage(): string;

    public function getType(): ?string;
}
