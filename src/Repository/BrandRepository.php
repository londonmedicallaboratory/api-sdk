<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use LML\SDK\Struct\Point;
use Webmozart\Assert\Assert;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Enum\DayOfWeekEnum;
use LML\SDK\Entity\Brand\Brand;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Brand\Calender\Slot;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Brand\TimeBlock\TimeBlock;
use LML\SDK\Entity\Brand\WorkingHours\WorkingHours;
use LML\SDK\Entity\HealthcareProfessional\HealthcareProfessional;
use function sprintf;
use function array_map;
use function Clue\React\Block\await;

/**
 * @psalm-import-type S from Brand
 * @psalm-import-type S from TimeBlock as TH
 * @psalm-import-type S from WorkingHours as WH
 *
 * @extends AbstractRepository<S, Brand, array>
 */
class BrandRepository extends AbstractRepository
{
    /**
     * Returns monthly availability array in format of
     * <code>
     *   "2022-12-31": true,
     * </code>
     *
     * @return array<string, bool>
     */
    public function getMonthlyCalender(string $id, DateTime $when): array
    {
        $url = sprintf('/test_location/calender/%04d/%02d', $when->format('Y'), $when->format('m'));

        /** @var PromiseInterface<array{id: string, availability: array<string, bool>}> $promise */
        $promise = $this->getClient()->getAsync(url: $url, cacheTimeout: 10, filters: ['brand_id' => $id]);
        $resolved = await($promise);

        return $resolved['availability'];
    }

    /**
     * @psalm-return ($await is true ? list<TimeBlock> : PromiseInterface<list<TimeBlock>>)
     */
    public function getTimeBlocks(bool $await = false): array|PromiseInterface
    {
        $url = '/test_location/timeblocks';

        /** @var PromiseInterface<list<TH>> $promise */
        $promise = $this->getClient()->getAsync(url: $url, cacheTimeout: 10);

        $resolvedPromise = $promise->then(fn($data) => array_map(static fn($datum) => new TimeBlock(
            id: $datum['id'],
            startsAt: new DateTime($datum['starts_at']),
            endsAt: new DateTime($datum['ends_at']),
            description: $datum['description'],
        ), $data));

        return $await ? await($resolvedPromise) : $resolvedPromise;
    }

    /**
     * @return list<Slot>
     */
    public function getSlots(string $id, DateTime $when): array
    {
        $url = sprintf('/test_location/slots/%04d/%02d/%02d', $when->format('Y'), $when->format('m'), $when->format('d'));

        /** @var PromiseInterface<list<array<mixed>>> $promise */
        $promise = $this->getClient()->getAsync(url: $url, cacheTimeout: 10, filters: ['brand_id' => $id]);

        $slots = await($promise);

        $results = [];
        foreach ($slots as $datum) {
            Assert::string($date = $datum['time'] ?? null);
            Assert::boolean($isAvailable = $datum['available'] ?? null);
            $results[] = new Slot(new DateTime($date), $isAvailable);
        }

        return $results;
    }

    /**
     * @psalm-return ($await is true ? list<WorkingHours> : PromiseInterface<list<WorkingHours>>)
     */
    public function getWorkHours(string $id, bool $await = false): PromiseInterface|array
    {
        $url = sprintf('/test_location/%s/workhours', $id);

        /** @var PromiseInterface<list<WH>|null> $promise */
        $promise = $this->getClient()->getAsync(url: $url, cacheTimeout: 10);

        $resolvedPromise = $promise->then(fn($data) => array_map(static fn($datum) => new WorkingHours(
            id: $datum['id'],
            dayOfWeek: DayOfWeekEnum::fromShortcut($datum['day_of_week']),
            startsAt: $datum['starts_at'],
            endsAt: $datum['ends_at'],
            isActive: $datum['active'] ?? true,
        ), $data ?? []));

        return $await ? await($resolvedPromise) : $resolvedPromise;
    }

    protected function one($entity, $options, $optimizer): Brand
    {
        $id = $entity['id'];
        $nextAvailableSlot = $entity['next_available_slot'] ?? null;
        $slot = $nextAvailableSlot ? new Slot(new DateTime($nextAvailableSlot), isAvailable: true) : null;

        $latitude = $entity['latitude'] ?? null;
        $longitude = $entity['longitude'] ?? null;
        $point = $latitude && $longitude ? new Point($latitude, $longitude) : null;

        return new Brand(
            id: $id,
            fullAddress: $entity['full_address'],
            city: $entity['city'],
            postalCode: $entity['postal_code'],
            name: $entity['name'],
            healthcareProfessionals: new LazyPromise($this->getProfessionals($id)),
            workHours: new LazyPromise($this->getWorkHours($id)),
            nearestBusStation: $entity['nearest_bus_station'] ?? null,
            nearestTrainStation: $entity['nearest_train_station'] ?? null,
            nextAvailableSlot: new ResolvedValue($slot),
            point: $point,
            distance: $entity['distance'] ?? null,
        );
    }

    protected function getCacheTimeout(): int
    {
        return 10;
    }

    /**
     * @return PromiseInterface<list<HealthcareProfessional>>
     */
    private function getProfessionals(string $id): PromiseInterface
    {
        $url = sprintf('/test_location/%s/professionals', $id);

        return $this->get(HealthcareProfessionalRepository::class)->findBy(url: $url);
    }
}
