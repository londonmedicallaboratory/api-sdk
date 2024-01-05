<?php

declare(strict_types=1);

namespace LML\SDK\Service;

use Webmozart\Assert\Assert;
use LML\SDK\Lazy\LazyPromise;
use React\Promise\PromiseInterface;
use LML\SDK\Service\Client\ClientInterface;
use function sprintf;
use function React\Promise\resolve;

/**
 * @psalm-type TSageAuth = array{vendor?: string, encryption_key?: string}
 *
 * @psalm-type TExpected = array{
 *      supported_countries: list<string>,
 *      name: string,
 *      code: string,
 *      sage_auth?: ?TSageAuth,
 * }
 */
class InformationBooth
{
    public function __construct(
        private ClientInterface $client,
    )
    {
    }

    /**
     * @return TExpected
     */
    public function getWebsiteInfo(): array
    {
        /** @var PromiseInterface<TExpected> $promise */
        $promise = $this->client->getAsync('/info/website', tag: 'information_booth');
        $lazy = new LazyPromise($promise);

        return $lazy->getValue();
    }

    /**
     * @return PromiseInterface<null>|PromiseInterface<null|array{latitude: float, longitude: float}>
     */
    public function getVisitorsCoordinates(?string $ip, ?string $search): PromiseInterface
    {
        if (!$ip && !$search) {
            return resolve(null);
        }
        $url = sprintf('/info/coordinates?ip=%s&wide_search=%s', (string)$ip, (string)$search);

        return $this->client->getAsync($url)
            ->then(function (mixed $data) {
                if (!$data) {
                    return null;
                }
                Assert::isArray($data);
                Assert::float($latitude = $data['latitude'] ?? null);
                Assert::float($longitude = $data['longitude'] ?? null);

                return [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ];
            });
    }
}
