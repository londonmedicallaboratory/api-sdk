<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client;

use React\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * @return PromiseInterface<mixed>
     */
    public function getAsync(string $url, array $filters = [], int $page = 1, ?int $cacheTimeout = null, ?string $tag = null): PromiseInterface;

    /**
     * @return PromiseInterface<ResponseInterface>
     */
    public function post(string $url, array $data): PromiseInterface;

    /**
     * @return PromiseInterface<ResponseInterface>
     */
    public function patch(string $url, string $id, array $data): PromiseInterface;

    /**
     * @return PromiseInterface<ResponseInterface>
     */
    public function delete(string $url, string $id): PromiseInterface;

    public function invalidate(string ...$tags): void;
}
