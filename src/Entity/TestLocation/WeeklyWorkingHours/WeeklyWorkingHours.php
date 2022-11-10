<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation\WeeklyWorkingHours;

use Traversable;
use IteratorAggregate;
use LML\SDK\Entity\TestLocation\WorkingHours\WorkingHours;

/**
 * @implements IteratorAggregate<WeeklyHoursPeriod>
 */
class WeeklyWorkingHours implements IteratorAggregate
{
    /**
     * @param list<WorkingHours> $workingDays
     */
    public function __construct(
        private array $workingDays,
    )
    {
    }

    /**
     * @param list<WorkingHours> $workingDays
     *
     * @return list<WeeklyHoursPeriod>
     */
    public function getPeriods(): array
    {
        $workingDays = $this->workingDays;
        usort(
            $workingDays,
            fn(
                WorkingHours $workingDayOne,
                WorkingHours $workingDayTwo
            ) => $workingDayOne->getDayOfWeek()->value > $workingDayTwo->getDayOfWeek()->value ? 1 : 0
        );

        $workingHourPeriods = [];
        $periodStartDay = null;

        foreach ($workingDays as $key => $workingDay) {
            $nextDay = $workingDays[$key + 1] ?? null;
            if (!$nextDay) {
                $workingHourPeriods[] = $this->generatePeriod($workingDay, $periodStartDay);
                break;
            }
            if ($this->shouldSkipDay($workingDay, $nextDay)) {
                if (null === $periodStartDay) {
                    $periodStartDay = $workingDay;
                }
                continue;
            }
            $workingHourPeriods[] = $this->generatePeriod($workingDay, $periodStartDay);
            $periodStartDay = null;
        }

        return $workingHourPeriods;
    }

    public function getIterator(): Traversable
    {
        yield from $this->getPeriods();
    }

    private function shouldSkipDay(WorkingHours $startDay, WorkingHours $checkDay): bool
    {
        return $startDay->isActive() && $checkDay->isActive() && $startDay->getStartsAt() === $checkDay->getStartsAt() && $startDay->getEndsAt() === $checkDay->getEndsAt();
    }

    private function generatePeriod(WorkingHours $workingDay, ?WorkingHours $startDay = null): WeeklyHoursPeriod
    {
        $daysPeriod = $startDay ? sprintf(
            '%s-%s',
            $startDay->getDayOfWeek()->getName(),
            $workingDay->getDayOfWeek()->getName()
        ) : $workingDay->getDayOfWeek()->getName();

        $hoursPeriod = $workingDay->isActive() ? sprintf('%s-%s', $workingDay->getStartsAt(), $workingDay->getEndsAt()) : 'Closed';

        return new WeeklyHoursPeriod($daysPeriod, $hoursPeriod);
    }
}
