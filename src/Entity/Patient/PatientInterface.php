<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Patient;

use Stringable;
use DateTimeInterface;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      first_name: string,
 *      last_name: string,
 *      gender: string,
 *      date_of_birth: string,
 *      phone_number?: ?string,
 *      ethnicity?: ?string,
 *      foreign_id?: ?string,
 *      email?: ?string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface PatientInterface extends ModelInterface, Stringable
{
    public function getFirstName(): string;

    public function getLastName(): string;

    public function getGender(): GenderEnum;

    public function getDateOfBirth(): DateTimeInterface;

    public function getEthnicity(): ?EthnicityEnum;

    public function getEmail(): ?string;
}
