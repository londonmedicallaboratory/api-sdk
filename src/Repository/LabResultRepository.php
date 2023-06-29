<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Biomarker\Biomarker;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\TestRegistration\LabResult;

/**
 * @psalm-import-type S from LabResult
 *
 * @extends AbstractRepository<S, LabResult, array{
 *     test_registration?: string,
 * }>
 */
class LabResultRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): LabResult
    {
        $labResult = new LabResult(
            id: $entity['id'],
            biomarker: new LazyPromise($this->getBiomarker($entity['biomarker_id'])),
            name: new ResolvedValue($entity['name']),
            code: new ResolvedValue($entity['code']),
            value: $entity['value'],
            isSuccessful: $entity['successful'],
            minRange: $entity['min_range'],
            maxRange: $entity['max_range'],
            comment: $entity['comment'] ?? null,
        );
        $labResult->errorReason = $entity['error_reason'] ?? null;
        $labResult->unitName = $entity['unit_type'] ?? null;
        $labResult->setHumanReadableValue($entity['human_readable_result']);

        return $labResult;
    }

    /**
     * @return PromiseInterface<Biomarker>
     */
    private function getBiomarker(string $id): PromiseInterface
    {
        return $this->get(BiomarkerRepository::class)->fetch($id);
    }
}