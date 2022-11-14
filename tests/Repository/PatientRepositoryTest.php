<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use DateTime;
use Exception;
use LogicException;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\SDK\Tests\AbstractTest;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\Address\Address;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\PatientRepository;

class PatientRepositoryTest extends AbstractTest
{
    private const ID = 'f18705fc-dcf1-405a-917c-aea2d735a30c';

    public function testCreate(): void
    {
        self::bootKernel();
        $repo = $this->getPatientRepository();

        $patient = new Patient(
            email: 'test@ex.de',
            firstName: 'Testing',
            lastName: 'Smith',
            dateOfBirth: new DateTime('-20 years'),
            ethnicity: EthnicityEnum::ASIAN_BANGLADESHI,
            gender: GenderEnum::FEMALE,
            address: new ResolvedValue(null),
        );

        $repo->persist($patient);
        $repo->flush();

        self::assertNotNull($patient->getId());
    }

    public function testOne(): void
    {
        self::bootKernel();
        $repo = $this->getPatientRepository();

        $patient = $repo->find(self::ID, await: true);
        $address = $patient->getAddress();
        self::assertInstanceOf(Address::class, $address);
    }

    public function testUpdate(): void
    {
        self::bootKernel();
        $repo = $this->getPatientRepository();

        $pagination = $repo->paginate(await: true);
        $nrOfResults = $pagination->getNrOfResults();
        $patient = $pagination->first() ?? throw new LogicException('No patient fixtures.');

        $randomName = sprintf('Randomizer-%s', random_int(1, 10_000));
        $patient->setFirstName($randomName);
        $repo->flush();
        $repo->clear();

        // let's load same patient, see if the cache has been invalidated after update
        $patient = $repo->find($patient->getId(), await: true) ?? throw new Exception('Patient is no longer existing.');
        self::assertEquals($randomName, $patient->getFirstName());

        // assert no new patient has been created
        $pagination = $repo->paginate(await: true);
        self::assertEquals($nrOfResults, $pagination->getNrOfResults());
    }

    public function testPagination(): void
    {
        self::bootKernel();
        $repo = $this->getPatientRepository();

        $pagination = $repo->paginate(await: true);
        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
    }

    private function getPatientRepository(): PatientRepository
    {
        return $this->getService(PatientRepository::class);
    }
}
