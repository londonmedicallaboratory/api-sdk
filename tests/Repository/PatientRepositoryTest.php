<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use DateTime;
use Exception;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\PaginatedResults;
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

    public function testUpdate(): void
    {
        self::bootKernel();
        /** @var PatientRepository $repo */
        $repo = self::$kernel->getContainer()->get(PatientRepository::class);
        $randomName = sprintf('Randomizer-%s', random_int(1, 10_000));

        $patient = $repo->find('ed0e6483-7f0e-4861-810c-5b3050005df1', await: true) ?? throw new Exception('No id.');
        self::assertInstanceOf(Patient::class, $patient);
        $patient->setFirstName($randomName);
        $patient->setDateOfBirth(new DateTime('2010-01-30'));
        $patient->setGender(GenderEnum::NON_BINARY);
        $repo->flush();

        // let's load same patient, see if the cache has been invalidated after update
        $patient = $repo->find('ed0e6483-7f0e-4861-810c-5b3050005df1', await: true) ?? throw new Exception('No id.');
        self::assertEquals($randomName, $patient->getFirstName());
    }

    public function testPagination(): void
    {
        self::bootKernel();
        /** @var PatientRepository $repo */
        $repo = self::$kernel->getContainer()->get(PatientRepository::class);

        $pagination = $repo->paginate(await: true);
        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
    }
}
