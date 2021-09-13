<?php

declare(strict_types=1);

namespace LML\SDK\Model\HealthcareProfessional;

use LML\SDK\Model\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      first_name: string,
 *      last_name: string,
 *      is_nurse: bool,
 *      is_lml_approved: bool,
 * }
 *
 * @extends ModelInterface<S>
 */
interface HealthcareProfessionalInterface extends ModelInterface
{
    public function getFirstName(): string;

    public function getLastName(): string;

    public function isNurse(): bool;

    public function isLMLApproved(): bool;
}
