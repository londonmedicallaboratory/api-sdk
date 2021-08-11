<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client;

use Closure;
use RuntimeException;
use React\Http\Browser;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use function sprintf;
use function json_encode;
use function str_replace;
use function json_decode;
use function array_merge;
use function base64_encode;
use function http_build_query;

class Client
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

    /**
     * @return PromiseInterface<mixed>
     *
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function getAsyncPromise(string $url, array $filters = [], int $page = 1): PromiseInterface
    {
        $queryParams = http_build_query(array_merge(['page' => $page], $filters));
        $url = str_replace('//', '/', $url);
        $url = $this->baseUrl . $url;
//        $url .= '/';

        if ($queryParams) {
            $url .= '?' . $queryParams;
        }

        $cacheKey = json_encode($url, JSON_THROW_ON_ERROR);
        $cache = $this->cache ?? throw new RuntimeException('You must set cache pool to use this feature.');

        $item = $cache->getItem($cacheKey);

        if ($item->isHit()) {
            return new Promise(function (Closure $resolve) use ($item) {
                $resolve($item->get());
            });
        }

        $token = sprintf('%s:%s', $this->username, $this->password);

        /** @psalm-suppress TooManyTemplateParams */
        return $this->browser->get($url, [
            'Authorization' => 'Basic ' . base64_encode($token),
        ])
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
}
