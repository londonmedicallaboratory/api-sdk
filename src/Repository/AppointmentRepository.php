<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use DateTimeInterface;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Entity\Brand\Brand;
use LML\SDK\Entity\Brand\Calender\DoctorSlot;
use LML\SDK\Entity\Brand\Calender\Slot;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Exception\DataNotFoundException;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Struct\Point;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use Webmozart\Assert\Assert;
use function React\Async\await;
use function React\Promise\resolve;

/**
 * @psalm-import-type S from Appointment
 *
 * @extends AbstractRepository<S, Appointment, array{
 *     start_date?: ?string|DateTimeInterface,
 *     end_date?: ?string|DateTimeInterface,
 * }>
 */
class AppointmentRepository extends AbstractRepository
{
    public function getPersistenceGraph(ModelInterface $view): iterable
    {
        yield $view->getPatient();
    }

    /**
     * @return array<string, bool>
     */
    public function getMonthlyCalender(DateTime $when): array
    {
        $url = sprintf('/appointment/video/calendar/%04d/%02d', $when->format('Y'), $when->format('m'));

        /** @var PromiseInterface<array{id: string, availability: array<string, bool>}> $promise */
        $promise = $this->getClient()->getAsync(url: $url, cacheTimeout: 10);
        $resolved = await($promise);

        return $resolved['availability'];
    }

    /**
     * @return list<Slot>
     */
    public function getSlots(DateTime $when): array
    {
        $url = sprintf('/appointment/video/slots/%04d/%02d/%02d', $when->format('Y'), $when->format('m'), $when->format('d'));

        /** @var PromiseInterface<list<array<mixed>>> $promise */
        $promise = $this->getClient()->getAsync(url: $url, cacheTimeout: 10);

        $slots = await($promise);

        $results = [];
        foreach ($slots as $datum) {
            $id = (string)($datum['id'] ?? throw new DataNotFoundException());
            Assert::string($date = $datum['time'] ?? null);
            Assert::boolean($isAvailable = $datum['available'] ?? null);
            $results[] = new Slot($id, new DateTime($date), $isAvailable);
        }

        return $results;
    }

    protected function one($entity, $options, $optimizer): Appointment
    {
        $id = $entity['id'] ?? null;

        $appointmentTime = $entity['starts_at'] ?? throw new DataNotFoundException();
        $endsAt = $entity['ends_at'] ?? null;
        $expiresAt = $entity['expires_at'] ?? null;

        $latitude = $entity['point']['latitude'] ?? null;
        $longitude = $entity['point']['longitude'] ?? null;
        $point = ($latitude !== null) && ($longitude !== null) ? new Point($latitude, $longitude) : null;

        return new Appointment(
            id: $id,
            type: $entity['type'],
            startsAt: new ResolvedValue(new DateTime($appointmentTime)),
            endsAt: $endsAt ? new ResolvedValue(new DateTime($endsAt)) : new ResolvedValue(null),
            location: new LazyPromise($this->getTestLocation($entity['test_location_id'])),
            brand: new LazyPromise($this->getBrand($entity['brand_id'])),
            products: new LazyPromise($this->getProducts($id)),
            patient: new LazyPromise($this->getPatient($entity['patient_id'])),
            isConfirmed: new ResolvedValue($entity['confirmed'] ?? false),
            timeId: new ResolvedValue($entity['time_id'] ?? null),
            expiresAt: new ResolvedValue($expiresAt ? new DateTime($expiresAt) : null),
            fullAddress: new ResolvedValue($entity['full_address'] ?? null),
            point: new ResolvedValue($point),
        );
    }

    /**
     * @return PromiseInterface<Brand>
     */
    private function getTestLocation(string $id): PromiseInterface
    {
        return $this->get(BrandRepository::class)->fetch($id);
    }

    /**
     * @return PromiseInterface<Brand>
     */
    private function getBrand(string $id): PromiseInterface
    {
        return $this->get(BrandRepository::class)->fetch($id);
    }

    /**
     * @return PromiseInterface<null|Patient>
     */
    private function getPatient(?string $id): PromiseInterface
    {
        return $this->get(PatientRepository::class)->find($id);
    }

    /**
     * @return PromiseInterface<list<Product>>
     */
    private function getProducts(?string $id): PromiseInterface
    {
        if (!$id) {
            return resolve([]);
        }
        $url = sprintf('/appointment/%s/products', $id);

        return $this->get(ProductRepository::class)->findBy(url: $url);
    }
}
