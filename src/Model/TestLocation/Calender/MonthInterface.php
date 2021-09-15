<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestLocation\Calender;

use LML\SDK\Model\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      availability: array<string, bool>,
 * }
 *
 * @extends ModelInterface<S>
 */
interface MonthInterface extends ModelInterface
{
    /**
     * @return list<DayInterface>
     */
    public function getDays();
}