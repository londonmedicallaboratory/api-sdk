<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use React\EventLoop\Loop;
use React\Promise\PromiseInterface;
use LML\SDK\Model\TestLocation\TestLocation;
use LML\SDK\ViewFactory\AbstractViewRepository;
use LML\SDK\Model\TestLocation\TestLocationInterface;
use function sprintf;
use function array_map;
use function Clue\React\Block\await;

/**
 * @psalm-import-type S from TestLocationInterface
 *
 * @extends AbstractViewRepository<S, TestLocationInterface, array>
 *
 * @see TestLocationInterface
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
        $promise = $this->getClient()->getAsync(url: $url);

        return await($promise, Loop::get());
    }

    /**
     * @return list<DateTime>
     */
    public function getSlots(string $id, DateTime $when)
    {
        $url = sprintf('/test_location/%s/slots/%04d/%02d', $id, $when->format('Y'), $when->format('m'));

        /** @var PromiseInterface<list<string>> $promise */
        $promise = $this->getClient()->getAsync(url: $url);

        $slots = await($promise, Loop::get());

        return array_map(fn(string $date) => new DateTime($date), $slots);
    }

    protected function one($entity, $options, $optimizer)
    {
        $id = $entity['id'];

        return new TestLocation(
            id: $id,
            name: $entity['name'],
            fullAddress: $entity['full_address'],
            city: $entity['city'],
            postalCode: $entity['postal_code'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/test_location';
    }
}
