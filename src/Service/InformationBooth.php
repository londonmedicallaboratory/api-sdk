<?php

declare(strict_types=1);

namespace LML\SDK\Service;

use LML\SDK\Lazy\LazyPromise;
use React\Promise\PromiseInterface;
use LML\SDK\Service\Client\ClientInterface;

/**
 * @psalm-type TSageAuth = array{vendor?: string, encryption_key?: string}
 *
 * @psalm-type TExpected = array{
 *      supported_countries: list<string>,
 *      name: string,
 *      code: string,
 *      sage_auth?: ?TSageAuth,
 * }
 */
class InformationBooth
{
    public function __construct(
        private ClientInterface $client,
    )
    {
    }

    /**
     * @return TExpected
     *
     * @noinspection PhpRedundantVariableDocTypeInspection Bug in PHPStorm
     */
    public function getWebsiteInfo()
    {
        /** @var PromiseInterface<TExpected> $promise */
        $promise = $this->client->getAsync('/info/website', tag: 'information_booth');
        $lazy = new LazyPromise($promise);

        return $lazy->getValue();
    }
}
