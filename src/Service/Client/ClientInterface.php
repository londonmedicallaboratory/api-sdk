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
    public function getAsync(string $url, array $filters = [], int $page = 1): PromiseInterface;

    /**
     * @return PromiseInterface<ResponseInterface>
     */
    public function post(string $url, array $data);

    /**
     * @return PromiseInterface<ResponseInterface>
     */
    public function patch(string $url, array $data): PromiseInterface;
}
