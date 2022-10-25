<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Repository\AppointmentRepository;

class AppointmentRepositoryTest extends AbstractTest
{
    public function testInvalidCode(): void
    {
        self::bootKernel();
        $repo = $this->getRepository();
        $appointment = $repo->fetch('99feb73b-ce3f-43c6-8e48-321ab13d760f', await: true);
        self::assertInstanceOf(Appointment::class, $appointment);
    }

    private function getRepository(): AppointmentRepository
    {
        return $this->getService(AppointmentRepository::class);
    }
}
