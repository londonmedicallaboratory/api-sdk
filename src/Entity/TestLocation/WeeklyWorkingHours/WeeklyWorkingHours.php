<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation\WeeklyWorkingHours;

use Traversable;
use IteratorAggregate;
use LML\SDK\Entity\TestLocation\WorkingHours\WorkingHoursInterface;

class WeeklyWorkingHours implements IteratorAggregate
{
    /**
     * @param list<WorkingHoursInterface> $workingDays
     */
    public function __construct(
        private array $workingDays,
    ) {

    }

    /**
     * @param list<WorkingHoursInterface> $workingDays
     *
     * @return list<WeeklyHoursPeriod>
     */
    public function getPeriods(): array
    {
        $workingDays = $this->workingDays;
        usort(
            $workingDays,
            fn(
                WorkingHoursInterface $workingDayOne,
                WorkingHoursInterface $workingDayTwo
            ) => $workingDayOne->getDayOfWeek()->value > $workingDayTwo->getDayOfWeek()->value ? 1 : 0
        );

        $workingHourPeriods = [];
        $periodStartDay = null;

        foreach ($workingDays as $workingDay) {
            $nextDay = next($workingDays);
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

    private function shouldSkipDay(WorkingHoursInterface $startDay, WorkingHoursInterface $checkDay): bool
    {
        return $startDay->isActive() && $checkDay->isActive() && $startDay->getStartsAt() === $checkDay->getStartsAt() && $startDay->getEndsAt(
            ) === $checkDay->getEndsAt();
    }

    private function generatePeriod(WorkingHoursInterface $workingDay, ?WorkingHoursInterface $startDay = null): WeeklyHoursPeriod
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
