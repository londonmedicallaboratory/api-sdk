<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client;

use RuntimeException;
use LML\SDK\Lazy\AsyncLazyValue;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function sprintf;
use function json_decode;
use function json_encode;
use function str_replace;

class Client
{
    private HttpClientInterface $client;

    public function __construct(
        private string $baseUrl,
        string $username,
        string $password,
        private ?CacheInterface $cache,
        private int $cacheExpiration,
    )
    {
        $this->client = HttpClient::createForBaseUri(
            $baseUrl,
            [
                'auth_basic' => [$username, $password],
            ]
        );
    }

//    public function getAsync(string $url, array $filters = [], int $page = 1)
//    {
//        $url = str_replace('//', '/', $url);
//        $url = $this->baseUrl . $url;
//
//        $filters['page'] = $page;
//        $options = [
//            'query' => $filters,
//        ];
//
//        $cacheKey = sprintf('%s-%s', $url, json_encode($options, JSON_THROW_ON_ERROR));
//
//        $response = $this->client->request('GET', $url, $options);
//
//        return new AsyncLazyValue($response);
//    }

    /**
     * @return array<string, mixed>
     *
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function get(string $url, array $filters = [], int $page = 1): array
    {
        $url = str_replace('//', '/', $url);
        $url = $this->baseUrl . $url;

        $filters['page'] = $page;
        $options = [
            'query' => $filters,
        ];

        $cacheKey = sprintf('%s-%s', $url, json_encode($options, JSON_THROW_ON_ERROR));
        $cache = $this->cache ?? throw new RuntimeException('You must set cache pool to use this feature.');

        return $cache->get($cacheKey, function (ItemInterface $item) use ($url, $options): array {
            $item->expiresAfter($this->cacheExpiration);

            return $this->client->request('GET', $url, $options)->toArray();
        });
    }
}
