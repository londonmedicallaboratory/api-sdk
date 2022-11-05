<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Appointment;

use DateTimeInterface;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\TestLocation\TestLocation;

/**
 * @template TLoc of TestLocation
 *
 * @psalm-type Item = array{product_id: string, quantity: int, product_sku?: ?string}
 *
 * @psalm-type S=array{
 *     id?: ?string,
 *     testlocation_id: string,
 *     appointment_time: string,
 *     product_id: ?string,
 *     patient_id: ?string,
 *     confirmed?: ?bool,
 * }
 *
 * @extends ModelInterface<S>
 */
interface AppointmentInterface extends ModelInterface
{
    /**
     * @return TLoc
     */
    public function getTestLocation(): TestLocation;

    public function getAppointmentTime(): DateTimeInterface;

    public function getProduct(): ?Product;

    public function getPatient(): ?Patient;
}
