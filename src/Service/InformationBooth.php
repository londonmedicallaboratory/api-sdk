<?php

declare(strict_types=1);

namespace LML\SDK\Service;

use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Service\Client\Client;
use React\Promise\PromiseInterface;

/**
 * @psalm-type TSageAuth = array{vendor?: string, encryption_key?: string}
 *
 * @psalm-type TExpected = array{name: string, code: string, sage_auth?: ?TSageAuth}
 */
class InformationBooth
{
    public function __construct(
        private Client $client,
    )
    {
    }

    /**
     * @return TExpected
     */
    public function getWebsiteInfo()
    {
        /** @var PromiseInterface<TExpected> $promise */
        $promise = $this->client->getAsync('/info/website');
        $lazy = new LazyPromise($promise);

        return $lazy->getValue();
    }
}
