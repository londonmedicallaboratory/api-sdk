<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use DateTime;
use DateTimeInterface;
use LML\SDK\Attribute\Entity;
use LML\View\Lazy\ResolvedValue;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Product\ProductInterface;
use LML\SDK\Entity\Address\AddressInterface;
use LML\SDK\Entity\Patient\PatientInterface;
use LML\SDK\Repository\TestRegistrationRepository;
use function array_map;

/**
 * @see \LML\SDK\Repository\TestRegistrationRepository::one()
 */
#[Entity(repositoryClass: TestRegistrationRepository::class, baseUrl: 'test_registration')]
class TestRegistration implements TestRegistrationInterface
{
    /**
     * @param ?LazyValueInterface<?AddressInterface> $selfIsolatingAddress
     * @param list<string> $transitCountries
     * @param LazyValueInterface<list<ProductInterface>> $products
     * @param LazyValueInterface<?PatientInterface> $patient
     * @param LazyValueInterface<?string> $downloadUrl
     * @param ?LazyValueInterface<?AddressInterface> $ukAddress
     * @param ?LazyValueInterface<bool> $resultsReady
     */
    public function __construct(
        protected LazyValueInterface $products,
        protected LazyValueInterface $patient,
        protected LazyValueInterface $downloadUrl,
        protected ?LazyValueInterface $resultsReady = null,
        protected DateTimeInterface $createdAt = new DateTime(),
        protected ?DateTimeInterface $completedAt = null,
        protected ?DateTimeInterface $patientRegisteredAt = null,
        protected ?LazyValueInterface $ukAddress = null,
        protected array $transitCountries = [],
        protected ?string $doctorsNote = null,
        protected ?string $doctorsName = null,
        protected string $id = '',
    )
    {
    }

    public function getPatient(): ?PatientInterface
    {
        return $this->patient->getValue();
    }

    public function setPatient(?PatientInterface $patient): void
    {
        $this->patient = new ResolvedValue($patient);
    }

    public function getProducts(): array
    {
        return $this->products->getValue();
    }

    public function getUkAddress(): ?AddressInterface
    {
        return $this->ukAddress?->getValue();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getCompletedAt(): ?DateTimeInterface
    {
        return $this->completedAt;
    }

    public function getPatientRegisteredAt(): ?DateTimeInterface
    {
        return $this->patientRegisteredAt;
    }

    public function getDoctorsNote(): ?string
    {
        return $this->doctorsNote;
    }

    public function getDoctorsName(): ?string
    {
        return $this->doctorsName;
    }

    public function getDownloadUrl(): ?string
    {
        return $this->downloadUrl->getValue();
    }

    public function toArray(): array
    {
        $patient = $this->getPatient();
        $productIds = array_map(static fn(ProductInterface $product) => $product->getId(), $this->getProducts());
        $productSkus = array_map(static fn(ProductInterface $product) => $product->getSku(), $this->getProducts());

        return [
            'id' => $this->getId(),
            'patient_id' => $patient?->getId(),
            'results_ready' => $this->resultsReady?->getValue() ?? false,
            'product_ids' => $productIds,
            'product_skus' => $productSkus,
            'email' => $patient?->getEmail(),
            'date_of_birth' => $patient?->getDateOfBirth()->format('Y-m-d'),
            'first_name' => $patient?->getFirstName(),
            'last_name' => $patient?->getLastName(),
            'ethnicity' => $patient?->getEthnicity()?->value,
            'gender' => $patient?->getGender()->value,
            'created_at' => $this->getCreatedAt()->format('Y-m-d'),
            'completed_at' => $this->getCompletedAt()?->format('Y-m-d'),
            'patient_registered_at' => $this->getPatientRegisteredAt()?->format('Y-m-d'),
            'uk_address' => $this->getUkAddress()?->toArray(),
            'doctors_note' => $this->getDoctorsNote(),
            'doctors_name' => $this->getDoctorsName(),
            'download_url' => $this->getDownloadUrl(),
        ];
    }
}
