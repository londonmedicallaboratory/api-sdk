<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use DateTime;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Repository\PatientRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PatientRepositoryTest extends KernelTestCase
{
    public function testCreate(): void
    {
        self::bootKernel();
        /** @var PatientRepository $repo */
        $repo = self::$kernel->getContainer()->get(PatientRepository::class);

        $patient = new Patient(
            email      : 'test@ex.de',
            firstName  : 'Testing',
            lastName   : 'Smith',
            dateOfBirth: new DateTime('-20 years'),
            ethnicity  : EthnicityEnum::ASIAN_BANGLADESHI,
            gender     : GenderEnum::FEMALE,
        );

        $repo->persist($patient);
        $repo->flush();

        self::assertNotNull($patient->getId());
    }
}
