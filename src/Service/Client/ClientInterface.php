<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client;

use React\Promise\PromiseInterface;

interface ClientInterface
{
    /**
     * @return PromiseInterface<mixed>
     */
    public function getAsync(string $url, array $filters = [], int $page = 1): PromiseInterface;
}
