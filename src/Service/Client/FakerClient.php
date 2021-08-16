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
use LML\SDK\Service\Client\Faker\CategoriesFaker;
use function sprintf;
use function str_starts_with;

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
            new CategoriesFaker(),
        ];
    }

    public function getAsync(string $url, array $filters = [], int $page = 1): PromiseInterface
    {
        foreach ($this->fakers as $faker) {
            foreach ($faker->getPaginatedData() as $prefix => $paginatedDatum) {
                if (str_starts_with($prefix, $url)) {
                    return new Promise(function (Closure $resolve) use ($paginatedDatum) {
                        $resolve($paginatedDatum);
                    });
                }
            }
        }

        throw new RuntimeException(sprintf('URL "%s" is not faked.', $url));
    }
}
