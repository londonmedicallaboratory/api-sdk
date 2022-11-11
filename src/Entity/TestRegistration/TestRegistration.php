<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use DateTime;
use DateTimeInterface;
use LML\SDK\Attribute\Entity;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Address\Address;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\Product\Product;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Repository\TestRegistrationRepository;
use function array_map;


/**
 * There is a bug in psalm that prevents `gender` and `ethnicity` to use Enums
 * Due to upcoming enum support, bug will probably not be fixed.
 *
 * @psalm-type S=array{
 *      id: string,
 *      patient_id?: ?string,
 *      results_ready: bool,
 *      product_ids?: list<string>,
 *      product_skus?: list<string>,
 *      biomarker_ids?: list<string>,
 *      biomarker_codes?: list<string>,
 *      email?: ?string,
 *      date_of_birth?: string,
 *      first_name: ?string,
 *      last_name: ?string,
 *      ethnicity?: ?string,
 *      gender?: ?string,
 *      mobile_phone_number?: ?string,
 *      nhs_number?: ?string,
 *      vaccination_status?: ?string,
 *      created_at?: ?string,
 *      completed_at?: ?string,
 *      patient_registered_at?: ?string,
 *      brand_code?: string,
 *      foreign_id?: ?string,
 *      country_from?: string,
 *      transport_type?: string,
 *      travel_number?: string,
 *      doctors_note?: ?string,
 *      doctors_name?: ?string,
 *      download_url?: ?string,
 *      trf_code?: ?string,
 *      appointment_id?: ?string,
 *      uk_address?: null|array{
 *          id: string,
 *          line1: string,
 *          line2?: ?string,
 *          line3?: ?string,
 *          postal_code: string,
 *          country_name?: string,
 *          country_code: string,
 *          city: string,
 *      },
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: TestRegistrationRepository::class, baseUrl: 'test_registration')]
class TestRegistration implements ModelInterface
{
    /**
     * @param ?LazyValueInterface<?Address> $selfIsolatingAddress
     * @param list<string> $transitCountries
     * @param LazyValueInterface<list<Product>> $products
     * @param LazyValueInterface<?Patient> $patient
     * @param LazyValueInterface<?string> $downloadUrl
     * @param LazyValueInterface<?string> $trfCode
     * @param ?LazyValueInterface<?Address> $ukAddress
     * @param ?LazyValueInterface<?Appointment> $appointment
     * @param ?LazyValueInterface<bool> $resultsReady
     */
    public function __construct(
        protected LazyValueInterface $products,
        protected LazyValueInterface $patient,
        protected LazyValueInterface $downloadUrl,
        protected LazyValueInterface $trfCode,
        protected ?LazyValueInterface $resultsReady = null,
        protected DateTimeInterface $createdAt = new DateTime(),
        protected ?DateTimeInterface $completedAt = null,
        protected ?DateTimeInterface $patientRegisteredAt = null,
        protected ?LazyValueInterface $ukAddress = null,
        protected ?LazyValueInterface $appointment = null,
        protected array $transitCountries = [],
        protected ?string $doctorsNote = null,
        protected ?string $doctorsName = null,
        protected string $id = '',
    )
    {
    }

    public function getPatient(): ?Patient
    {
        return $this->patient->getValue();
    }

    public function setPatient(?Patient $patient): void
    {
        $this->patient = new ResolvedValue($patient);
    }

    /**
     * @return list<Product>
     */
    public function getProducts(): array
    {
        return $this->products->getValue();
    }

    public function getUkAddress(): ?Address
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

    public function getTrfCode(): ?string
    {
        return $this->trfCode->getValue();
    }

    public function getAppointment(): ?Appointment
    {
        return $this->appointment?->getValue();
    }

    public function setAppointment(?Appointment $appointment): void
    {
        $this->appointment = new ResolvedValue($appointment);
    }

    public function toArray(): array
    {
        $patient = $this->getPatient();
        $productIds = array_map(static fn(Product $product) => $product->getId(), $this->getProducts());
        $productSkus = array_map(static fn(Product $product) => $product->getSku(), $this->getProducts());

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
            'appointment_id' => $this->getAppointment()?->getId(),
        ];
    }
}
