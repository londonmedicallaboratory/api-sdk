<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client;

use Closure;
use RuntimeException;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use LML\SDK\Service\Client\Faker\FilesFaker;
use LML\SDK\Service\Client\Faker\ProductFaker;
use LML\SDK\Service\Client\Faker\ShippingFaker;
use LML\SDK\Service\Client\Faker\FakerInterface;
use LML\SDK\Service\Client\Faker\BiomarkerFaker;
use LML\SDK\Service\Client\Faker\ProductCategoriesFaker;
use LML\SDK\Service\Client\Faker\BiomarkerCategoriesFaker;
use function sprintf;

class FakerClient implements ClientInterface
{
    /**
     * @var list<FakerInterface>
     */
    private array $fakers;

    public function __construct()
    {
        $this->fakers = [
            new ProductFaker(),
            new BiomarkerFaker(),
            new ShippingFaker(),
            new FilesFaker(),
            new BiomarkerCategoriesFaker(),
            new ProductCategoriesFaker(),
        ];
    }

    public function getAsync(string $url, array $filters = [], int $page = 1, ?int $limit = null, ?int $cacheTimeout = null, ?string $tag = null): PromiseInterface
    {
        foreach ($this->fakers as $faker) {
            foreach ($faker->getPaginatedData() as $prefix => $paginatedDatum) {
                if ($prefix === $url) {
                    return new Promise(function (Closure $resolve) use ($paginatedDatum) {
                        $resolve($paginatedDatum);
                    });
                }
            }
        }

        throw new RuntimeException(sprintf('URL "%s" is not faked.', $url));
    }

    public function post(string $url, array $data): never
    {
        throw new RuntimeException('Not supported for faker.');
    }

    public function invalidate(string ...$tags): void
    {
        throw new RuntimeException('Not supported for faker.');
    }

    public function delete(string $url, string $id): never
    {
        throw new RuntimeException('Not supported for faker.');
    }

    public function patch(string $url, string $id, array $data): never
    {
        throw new RuntimeException('Not implemented.');
    }
}
