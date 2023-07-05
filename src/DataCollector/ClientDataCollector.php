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

/**
 * @property array{requests: null|list<array{url: string, cached: bool, method: string, filters: array}>} $data
 *
 * @psalm-suppress MixedReturnTypeCoercion - It is OK to suppress mixed here, we don't really need static analysis here.
 */
class ClientDataCollector extends AbstractDataCollector implements ClientInterface
{
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
        return $this->data['requests'] ?? [];
    }

    public function getAsync(string $url, array $filters = [], int $page = 1, ?int $limit = null, ?int $cacheTimeout = null, ?string $tag = null, array $extraQueryParams = []): PromiseInterface
    {
        $promise = $this->client->getAsync($url, $filters, $page, $limit, $cacheTimeout, tag: $tag);
        $isCached = $promise instanceof CachedItemPromise;

        return $this->logPromise($promise, $url, 'GET', $isCached, filters: $filters);
    }

    public function post(string $url, array $data): PromiseInterface
    {
        $promise = $this->client->post($url, $data);

        return $this->logPromise($promise, $url, 'POST', data: $data);
    }

    public function patch(string $url, string $id, array $data): PromiseInterface
    {
        $promise = $this->client->patch($url, $id, $data);

        return $this->logPromise($promise, $url, 'PATCH', data: $data);
    }

    public function delete(string $url, string $id): PromiseInterface
    {
        $this->data['requests'][] = [
            'url' => $url,
            'cached' => false,
            'method' => 'DELETE',
            'filters' => [],
        ];

        return $this->client->delete($url, $id);
    }

    public function invalidate(string ...$tags): void
    {
        $this->client->invalidate(...$tags);
    }

    public function collect(Request $request, Response $response, Throwable $exception = null): void
    {
    }

    public function getName(): string
    {
        return 'lml_sdk.client_collector';
    }

    private function logPromise(PromiseInterface $promise, string $url, string $method, bool $isCached = false, array $data = [], array $filters = []): PromiseInterface
    {
        $promise->then(function (mixed $response) use ($url, $method, $isCached, $filters, $data): mixed {
            $this->data['requests'][] = [
                'url' => $url,
                'cached' => $isCached,
                'method' => $method,
                'filters' => $filters,
                'data' => $data,
                'response' => $response,
            ];

            return $response;
        });

        return $promise;
    }
}
