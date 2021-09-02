<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Model\TestLocation\TestLocation;
use LML\SDK\ViewFactory\AbstractViewRepository;
use LML\SDK\Model\TestLocation\TestLocationInterface;
use function sprintf;

/**
 * @psalm-import-type S from TestLocationInterface
 *
 * @extends AbstractViewRepository<S, TestLocationInterface, array>
 *
 * @see TestLocationInterface
 */
class TestLocationRepository extends AbstractViewRepository
{
    protected function one($entity, $options, $optimizer)
    {
        $id = $entity['id'];

        return new TestLocation(
            id: $id,
            fullAddress: $entity['full_address'],
        );
    }

//    private function getCalender(string $id)
//    {
//        $url = sprintf('/test_location/%s/biomarkers', $id);
//    }

    protected function getBaseUrl(): string
    {
        return '/test_location';
    }
}
