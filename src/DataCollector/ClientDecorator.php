<?php

declare(strict_types=1);

namespace LML\SDK\DataCollector;

use Throwable;
use React\Promise\PromiseInterface;
use LML\SDK\Promise\CachedItemPromise;
use LML\SDK\Service\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;

class ClientDecorator extends AbstractDataCollector implements ClientInterface
{
    /**
     * @psalm-suppress NonInvariantDocblockPropertyType
     *
     * @var array{
     *     requests: list<array{url: string, cached: bool, method: string, filters: array}>,
     * }
     */
    protected $data = [
        'requests' => [],
    ];

    public function __construct(
        private ClientInterface $client,
    )
    {
    }

    /**
     * @return list<array{url: string, cached: bool}>
     *
     * Used in client_collector.html.twig
     */
    public function getRequests(): array
    {
        return $this->data['requests'];
    }

    public function getAsync(string $url, array $filters = [], int $page = 1, ?int $cacheTimeout = null): PromiseInterface
    {
        $promise = $this->client->getAsync($url, $filters, $page, $cacheTimeout);

        $isCached = $promise instanceof CachedItemPromise;
        $this->data['requests'][] = ['url' => $url, 'cached' => $isCached, 'method' => 'GET', 'filters' => $filters];

        return $promise;
    }

    public function post(string $url, array $data): PromiseInterface
    {
        $this->data['requests'][] = ['url' => $url, 'cached' => false, 'method' => 'POST', 'filters' => []];

        return $this->client->post($url, $data);
    }

    public function patch(string $url, array $data): PromiseInterface
    {
        $this->data['requests'][] = ['url' => $url, 'cached' => false, 'method' => 'PATCH', 'filters' => []];

        return $this->client->patch($url, $data);
    }

    public function collect(Request $request, Response $response, Throwable $exception = null): void
    {
    }

    public function getName(): string
    {
        return 'lml_sdk.client_collector';
    }
}
