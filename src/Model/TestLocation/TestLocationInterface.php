<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestLocation;

use LML\SDK\Model\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      full_address: string,
 *      city: string,
 *      postal_code: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface TestLocationInterface extends ModelInterface
{
    public function getName(): string;

    public function getFullAddress(): string;

    public function getCity(): string;

    public function getPostalCode(): string;
}
