<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client;

use RuntimeException;
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

        return $cache->get($cacheKey, function (ItemInterface $item) use ($url, $options): mixed {
            $item->expiresAfter($this->cacheExpiration);
            $content = $this->client->request('GET', $url, $options)->getContent();

            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        });
    }
}
