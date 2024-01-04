<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client;

use Closure;
use JsonException;
use RuntimeException;
use React\Http\Browser;
use React\Promise\PromiseInterface;
use LML\SDK\Service\Visitor\Visitor;
use LML\SDK\Promise\CachedItemPromise;
use Symfony\Component\Cache\CacheItem;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\ResponseException;
use Symfony\Contracts\Service\ResetInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use function rtrim;
use function ltrim;
use function sprintf;
use function array_map;
use function str_replace;
use function json_decode;
use function array_merge;
use function json_encode;
use function preg_replace;
use function base64_encode;
use function http_build_query;

class Client implements ClientInterface, ResetInterface
{
    /**
     * @var array<string, PromiseInterface>
     */
    private array $promiseMap = [];

    private Browser $browser;

    public function __construct(
        private string $baseUrl,
        private string $apiToken,
        private ?TagAwareAdapterInterface $cache,
        private int $cacheExpiration,
        private Visitor $visitor,
    )
    {
        $this->browser = new Browser();
    }

    public function reset(): void
    {
        $this->promiseMap = [];
    }

    public function patch(string $url, string $id, array $data): PromiseInterface
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $url = ltrim($url, '/');

        $url = sprintf('%s/%s/%s', $baseUrl, $url, $id);
        unset($data['id']);
        if ($affiliateCode = $this->visitor->getAffiliateCode()) {
            $url .= '?affiliate_code=' . $affiliateCode;
        }

        return $this->browser->patch($url, $this->getAuthHeaders(), json_encode($data, JSON_THROW_ON_ERROR));
    }

    public function post(string $url, array $data): PromiseInterface
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $url = ltrim($url, '/');

        $url = sprintf('%s/%s/', $baseUrl, $url);
        if ($affiliateCode = $this->visitor->getAffiliateCode()) {
            $url .= '?affiliate_code=' . $affiliateCode;
        }

        return $this->browser->post($url, $this->getAuthHeaders(), json_encode($data, JSON_THROW_ON_ERROR));
    }

    public function delete(string $url, string $id): PromiseInterface
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $url = ltrim($url, '/');

        $url = sprintf('%s/%s/%s', $baseUrl, $url, $id);
        if ($affiliateCode = $this->visitor->getAffiliateCode()) {
            $url .= '?affiliate_code=' . $affiliateCode;
        }

        return $this->browser->delete($url, $this->getAuthHeaders());
    }

    /**
     * @param int|null $cacheTimeout *
     */
    public function getAsync(string $url, array $filters = [], int $page = 1, ?int $limit = null, ?int $cacheTimeout = null, ?string $tag = null, array $extraQueryParams = []): PromiseInterface
    {
        if ($affiliateCode = $this->visitor->getAffiliateCode()) {
            $filters['affiliate_code'] = $affiliateCode;
        }
        $tag = $tag ? preg_replace('/\W/', '', $tag) : null;
//        dump($extraQueryParams);

        $url = $this->createRealUrl($url, $filters, $page, $limit, extraQueryParams: $extraQueryParams);
        $cache = $this->getCache();
        $cacheKey = $this->createCacheKey($url);

        $item = $cache->getItem($cacheKey);
        if ($item->isHit()) {
            return new CachedItemPromise(function (Closure $resolve) use ($item) {
                $resolve($item->get());
            });
        }

        return $this->promiseMap[$url] ??= $this->doGetPromise($url, $item, $cacheTimeout ?? $this->cacheExpiration, $tag);
    }

    public function invalidate(string ...$tags): void
    {
        $cache = $this->cache ?? throw new RuntimeException('You must set cache pool to use this feature.');
        $tags = array_map(static fn(string $tag) => preg_replace('/\W/', '', $tag), $tags);
        $cache->invalidateTags($tags);
    }

    public function doGetPromise(string $url, CacheItem $item, int $cacheTimeout, ?string $tag): PromiseInterface
    {
        return $this->browser->get($url, $this->getAuthHeaders())
            ->then(
                onFulfilled: function (ResponseInterface $response) use ($item, $cacheTimeout, $tag): array {
                    $body = (string)$response->getBody();
                    try {
                        /** @var array<string, mixed> $data */
                        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
                    } catch (JsonException) {
                        $data = [];
                    }

                    $item->expiresAfter($cacheTimeout);
                    $item->set($data);
                    if ($tag) {
                        $item->tag($tag);
                    }
                    $this->getCache()->save($item);

                    return $data;
                },
                onRejected: function (ResponseException $_e) use ($item, $cacheTimeout) {
                    $item->expiresAfter($cacheTimeout);
                    $item->set(null);

                    return null;
                }
            );
    }

    private function getCache(): TagAwareAdapterInterface
    {
        return $this->cache ?? throw new RuntimeException('You must set cache pool to use this feature.');
    }

    private function createRealUrl(string $url, array $filters = [], int $page = 1, ?int $limit = null, array $extraQueryParams = []): string
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $url = ltrim($url, '/');

        $url = sprintf('%s/%s', $baseUrl, $url);

        $extras = [];
        if ($page !== 1) {
            $extras['page'] = $page;
        }
        if ($limit) {
            $extras['limit'] = $limit;
        }
        $merge = array_merge($extras, $filters, $extraQueryParams);
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
