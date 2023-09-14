<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use DateTimeInterface;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\Brand\Brand;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\ModelInterface;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Appointment\Appointment;
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

    protected function one($entity, $options, $optimizer): Appointment
    {
        $id = $entity['id'] ?? null;

        return new Appointment(
            id: $id,
            appointmentTime: new ResolvedValue(new DateTime($entity['appointment_time'])),
            brand: new LazyPromise($this->getTestLocation($entity['brand_id'])),
            products: new LazyPromise($this->getProducts($id)),
            patient: new LazyPromise($this->getPatient($entity['patient_id'])),
            isConfirmed: new ResolvedValue($entity['confirmed'] ?? false),
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
