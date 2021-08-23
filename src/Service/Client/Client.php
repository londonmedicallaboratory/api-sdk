<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client;

use Closure;
use RuntimeException;
use React\Http\Browser;
use React\Promise\Promise;
use LML\SDK\Lazy\LazyPromise;
use React\Promise\PromiseInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use function rtrim;
use function ltrim;
use function sprintf;
use function str_replace;
use function json_decode;
use function array_merge;
use function json_encode;
use function base64_encode;
use function http_build_query;

class Client implements ClientInterface
{
    private Browser $browser;

    public function __construct(
        private string $baseUrl,
        private string $username,
        private string $password,
        private ?CacheItemPoolInterface $cache,
        private int $cacheExpiration,
    )
    {
        $this->browser = new Browser();
    }

    public function post(string $url, array $data)
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $url = ltrim($url, '/');

        $url = sprintf('%s/%s/', $baseUrl, $url);

        return $this->browser->post($url, $this->getAuthHeaders(), json_encode($data, JSON_THROW_ON_ERROR));
    }

    /**
     * @return PromiseInterface<mixed>
     */
    public function getAsync(string $url, array $filters = [], int $page = 1): PromiseInterface
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $url = ltrim($url, '/');

        $url = sprintf('%s/%s', $baseUrl, $url);

        $queryParams = http_build_query(array_merge(['page' => $page], $filters));
        if ($queryParams) {
            $url .= '?' . $queryParams;
        }

        $cache = $this->cache ?? throw new RuntimeException('You must set cache pool to use this feature.');

        // make unique key with reserved-characters protection
        $cacheKey = base64_encode($url);
        $cacheKey = str_replace('/', '-', $cacheKey);  // base64 allows trailing slash; it is one of reserved characters
        $item = $cache->getItem($cacheKey);

        if ($item->isHit()) {
            return new Promise(function (Closure $resolve) use ($item) {
                $resolve($item->get());
            });
        }

        return $this->browser->get($url, $this->getAuthHeaders())
            ->then(function (ResponseInterface $response) use ($item, $cache): array {
                $body = (string)$response->getBody();
                /** @var array<string, mixed> $data */
                $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
                $item->expiresAfter($this->cacheExpiration);
                $item->set($data);
                $cache->save($item);

                return $data;
            });
    }

    /**
     * @return array<string, string>
     */
    private function getAuthHeaders(): array
    {
        $token = sprintf('%s:%s', $this->username, $this->password);

        return [
            'Authorization' => 'Basic ' . base64_encode($token),
        ];
    }
}
