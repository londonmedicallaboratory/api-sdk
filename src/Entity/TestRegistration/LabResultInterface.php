<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      code: string,
 *      value: bool|string,
 *      min_range: ?string,
 *      max_range: ?string,
 *      comment?: ?string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface LabResultInterface extends ModelInterface
{
    public function getName(): string;

    public function getValue(): bool|string;

    public function getCode(): string;

    public function getMinRange(): ?string;

    public function getMaxRange(): ?string;

    public function getComment(): ?string;
}
