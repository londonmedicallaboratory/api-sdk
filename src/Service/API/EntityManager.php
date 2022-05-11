<?php

declare(strict_types=1);

namespace LML\SDK\Service\API;

use LogicException;
use ReflectionClass;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use Psr\Http\Message\ResponseInterface;
use LML\SDK\Service\Client\ClientInterface;
use LML\SDK\Util\ReflectionAttributeReader;
use Symfony\Component\DependencyInjection\ServiceLocator;
use function Clue\React\Block\awaitAll;

/**
 * Doctrine equivalent for models
 */
class EntityManager
{
    /**
     * @var array<string, ModelInterface>
     */
    private array $newEntities = [];

    /**
     * @var array<string, ModelInterface>
     */
    private array $entitiesToBeDeleted = [];

    /**
     * @var array<string, ModelInterface>
     */
    private array $managed = [];

    /**
     * @param ServiceLocator<class-string, AbstractRepository> $repositories
     */
    public function __construct(
        private ServiceLocator  $repositories,
        private ClientInterface $client,
    )
    {
    }

    public function persist(ModelInterface $model): void
    {
        $oid = spl_object_hash($model);
        if (isset($this->newEntities[$oid]) || isset($this->managed[$oid])) {
            return;
        }

        $this->newEntities[$oid] = $model;
    }

    public function remove(ModelInterface $model): void
    {
        $oid = spl_object_hash($model);
        if (!isset($this->managed[$oid])) {
            return;
        }

        $this->entitiesToBeDeleted[$oid] = $model;
    }

    public function flush(): void
    {
        $promises = [];
        foreach ($this->newEntities as $entity) {
            $baseUrl = $this->getBaseUrl(get_class($entity));

            $promises[] = $this->client->post($baseUrl, $entity->toArray())->then(function (ResponseInterface $response) use ($entity) {
                $body = (string)$response->getBody();
                $data = (array)json_decode($body, false, 512, JSON_THROW_ON_ERROR);
                $id = (string)($data['id']);
                $rc = new ReflectionClass($entity);
                $property = $rc->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($entity, $id);
            });
        }

        foreach ($this->managed as $entity) {
            $baseUrl = $this->getBaseUrl(get_class($entity));
            $promises[] = $this->client->patch($baseUrl . '/' . $entity->getId(), $entity->toArray());
        }

        foreach ($this->entitiesToBeDeleted as $entity) {
            $baseUrl = $this->getBaseUrl(get_class($entity));
            $promises[] = $this->client->delete($baseUrl, $entity->getId());
        }

        awaitAll($promises);

        foreach ($this->newEntities as $oid => $entity) {
            $this->managed[$oid] = $entity;
        }
        $this->newEntities = [];
        $this->entitiesToBeDeleted = [];
    }

    /**
     * @param class-string $className
     */
    public function getRepository(string $className): AbstractRepository
    {
        return $this->repositories->get($className);
    }

    /**
     * @param class-string $className
     */
    private function getBaseUrl(string $className): string
    {
        $attribute = ReflectionAttributeReader::getAttribute($className, Entity::class);

        $url = $attribute?->getBaseUrl() ?? throw new LogicException(sprintf('Model %s is not properly configured, missing %s attribute.', $className, Entity::class));

        return trim($url, '/');
    }
}
