<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client;

use Closure;
use RuntimeException;
use React\Http\Browser;
use React\Promise\PromiseInterface;
use LML\SDK\Promise\CachedItemPromise;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
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
        private string                    $baseUrl,
        private string                    $apiToken,
        private ?TagAwareAdapterInterface $cache,
        private int                       $cacheExpiration,
    )
    {
        $this->browser = new Browser();
    }

    public function patch(string $url, string $id, array $data): PromiseInterface
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $url = ltrim($url, '/');

        $url = sprintf('%s/%s/%s', $baseUrl, $url, $id);
        unset($data['id']);

        return $this->browser->patch($url, $this->getAuthHeaders(), json_encode($data, JSON_THROW_ON_ERROR));
    }

    public function post(string $url, array $data): PromiseInterface
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $url = ltrim($url, '/');

        $url = sprintf('%s/%s/', $baseUrl, $url);

        return $this->browser->post($url, $this->getAuthHeaders(), json_encode($data, JSON_THROW_ON_ERROR));
    }

    public function delete(string $url, string $id): PromiseInterface
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $url = ltrim($url, '/');

        $url = sprintf('%s/%s/%s', $baseUrl, $url, $id);

        return $this->browser->delete($url);
    }

    /**
     * @param int|null $cacheTimeout *
     *
     * @return PromiseInterface<mixed>
     */
    public function getAsync(string $url, array $filters = [], int $page = 1, ?int $cacheTimeout = null, ?string $tag = null): PromiseInterface
    {
        $tag = $tag ? preg_replace('/\W/', '', $tag) : null;

        $url = $this->createRealUrl($url, $filters, $page);
        $cache = $this->cache ?? throw new RuntimeException('You must set cache pool to use this feature.');
        $cacheKey = $this->createCacheKey($url);

        $item = $cache->getItem($cacheKey);
        if ($item->isHit()) {
            return new CachedItemPromise(function (Closure $resolve) use ($item) {
                $resolve($item->get());
            });
        }

        $cacheTimeout ??= $this->cacheExpiration;

        return $this->browser->get($url, $this->getAuthHeaders())
            ->then(static function (ResponseInterface $response) use ($item, $cache, $cacheTimeout, $tag): array {
                $body = (string)$response->getBody();
                /** @var array<string, mixed> $data */
                $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

                $item->expiresAfter($cacheTimeout);
                $item->set($data);
                if ($tag) {
                    $item->tag($tag);
                }
                $cache->save($item);

                return $data;
            });
    }

    public function invalidate(string ...$tags): void
    {
        $cache = $this->cache ?? throw new RuntimeException('You must set cache pool to use this feature.');
        $tags = array_map(static fn(string $tag) => preg_replace('/\W/', '', $tag), $tags);
        $cache->invalidateTags($tags);
    }

    private function createRealUrl(string $url, array $filters = [], int $page = 1): string
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $url = ltrim($url, '/');

        $url = sprintf('%s/%s', $baseUrl, $url);

        $extras = [];
        if ($page !== 1) {
            $extras['page'] = $page;
        }
        $merge = array_merge($extras, $filters);
        $queryParams = http_build_query($merge);
        if ($queryParams) {
            $url .= '?' . $queryParams;
        }

        return $url;
    }

    private function createCacheKey(string $url): string
    {
        // make unique key with reserved-characters protection
        $cacheKey = base64_encode($url);

        return str_replace('/', '-', $cacheKey);  // base64 allows trailing slash; it is one of reserved characters
    }

    /**
     * @return array<string, string>
     */
    private function getAuthHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiToken,
        ];
    }
}
