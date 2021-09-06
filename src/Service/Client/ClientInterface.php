<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client;

use React\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * @param int|null $cacheTimeout *
     *
     * @return PromiseInterface<mixed>
     */
    public function getAsync(string $url, array $filters = [], int $page = 1, ?int $cacheTimeout = null): PromiseInterface;

    /**
     * @return PromiseInterface<ResponseInterface>
     */
    public function post(string $url, array $data);

    /**
     * @return PromiseInterface<ResponseInterface>
     */
    public function patch(string $url, array $data): PromiseInterface;
}
