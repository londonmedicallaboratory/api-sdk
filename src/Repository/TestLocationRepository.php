<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use React\EventLoop\Loop;
use LML\SDK\Lazy\LazyPromise;
use React\Promise\PromiseInterface;
use LML\SDK\Model\TestLocation\TestLocation;
use LML\SDK\ViewFactory\AbstractViewRepository;
use LML\SDK\Model\TestLocation\TestLocationInterface;
use LML\SDK\Model\HealthcareProfessional\HealthcareProfessional;
use function sprintf;
use function array_map;
use function Clue\React\Block\await;

/**
 * @psalm-import-type S from TestLocationInterface
 *
 * @extends AbstractViewRepository<S, TestLocationInterface, array>
 */
class TestLocationRepository extends AbstractViewRepository
{
    /**
     * @return array{availability: array<string, bool>, id: string}
     */
    public function getMonthlyCalender(string $id, DateTime $when)
    {
        $url = sprintf('/test_location/%s/calender/%04d/%02d', $id, $when->format('Y'), $when->format('m'));

        /** @var PromiseInterface<array{id: string, availability: array<string, bool>}> $promise */
        $promise = $this->getClient()->getAsync(url: $url, cacheTimeout: 10);

        return await($promise, Loop::get());
    }

    /**
     * @return list<DateTime>
     */
    public function getSlots(string $id, DateTime $when)
    {
        $url = sprintf('/test_location/%s/slots/%04d/%02d/%02d', $id, $when->format('Y'), $when->format('m'), $when->format('d'));

        /** @var PromiseInterface<list<string>> $promise */
        $promise = $this->getClient()->getAsync(url: $url, cacheTimeout: 10);

        $slots = await($promise, Loop::get());

        return array_map(fn(string $date) => new DateTime($date), $slots);
    }

    protected function one($entity, $options, $optimizer): TestLocation
    {
        $id = $entity['id'];

        return new TestLocation(
            id: $id,
            name: $entity['name'],
            fullAddress: $entity['full_address'],
            city: $entity['city'],
            postalCode: $entity['postal_code'],
            healthcareProfessionals: new LazyPromise($this->getProfessionals($id)),
        );
    }

    protected function getBaseUrl(): string
    {
        return '/test_location';
    }

    /**
     * @return PromiseInterface<list<HealthcareProfessional>>
     */
    private function getProfessionals(string $id)
    {
        $url = sprintf('/test_location/%s/professionals', $id);

        return $this->get(HealthcareProfessionalRepository::class)->findBy(url: $url);
    }
}
