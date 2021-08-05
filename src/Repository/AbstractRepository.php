<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use RuntimeException;
use LML\SDK\Service\Client\Client;
use LML\SDK\Factory\ModelMapperInterface;
use LML\SDK\Service\IdentityMap\IdentityMap;

/**
 * @template T of object
 * @template R of ModelMapperInterface
 *
 * @implements RepositoryInterface<T>
 *
 * @see ModelMapperInterface
 */
abstract class AbstractRepository implements RepositoryInterface
{
    private ?Client $client = null;
    private ?IdentityMap $identityMap = null;

    /**
     * @var ModelMapperInterface<T, array>
     */
    private ModelMapperInterface $mapper;

    /**
     * @param class-string<T> $className
     * @param class-string<R> $mapperName
     *
     * @psalm-suppress PossiblyInvalidPropertyAssignmentValue
     */
    public function __construct(private string $className, string $mapperName)
    {
        $this->mapper = new $mapperName();
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function find(string $id)
    {
        return $this->getIdentityMap()->get($this->className, $id, fn() => $this->createOne($this->fetchFromAPI($id)));
    }

    public function getIdentityMap(): IdentityMap
    {
        return $this->identityMap ?? throw new RuntimeException('DI failure.');
    }

    public function setIdentityMap(IdentityMap $identityMap): void
    {
        $this->identityMap = $identityMap;
    }

    /**
     * @param array<string, mixed> $input
     *
     * @return T
     */
    protected function createOne(array $input)
    {
        $mapper = $this->mapper;

        return $mapper->fromArray($input);
    }

    abstract protected function getBaseUrl(): string;

    /**
     * @return array<string, mixed>
     *
     */
    private function fetchFromAPI(string $id)
    {
        $url = $this->getBaseUrl() . '/' . $id;

        return $this->getClient()->get($url);
    }

    private function getClient(): Client
    {
        return $this->client ?? throw new RuntimeException('DI failure.');
    }
}
