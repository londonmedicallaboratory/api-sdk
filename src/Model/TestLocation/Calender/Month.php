<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestLocation\Calender;

use function sprintf;

class Month implements MonthInterface
{
    public function __construct(
        public int $year,
        public int $month,
    )
    {
    }

    public function getDays()
    {
        return [];
    }

    public function getId(): string
    {
        return sprintf('%04d-%02d', $this->year, $this->month);
    }

    public function toArray()
    {
        $availability = [];
        foreach ($this->getDays() as $day) {
            $availability[$day->format()] = $day->isAvailable();
        }

        return [
            'id'           => $this->getId(),
            'availability' => $availability,
        ];
    }
}
