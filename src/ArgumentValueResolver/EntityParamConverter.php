<?php

declare(strict_types=1);

namespace LML\SDK\ArgumentValueResolver;

use RuntimeException;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\Basket\Basket;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Service\API\EntityManager;
use LML\SDK\Util\ReflectionAttributeReader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use function is_a;
use function sprintf;

/**
 * Allows param conversion of model, similar to Doctrine's entity value resolver.
 *
 * Only slug and id are supported.
 */
class EntityParamConverter implements ValueResolverInterface
{
    public function __construct(
        private EntityManager $modelManager,
    ) {
    }

    /**
     * @return iterable<ModelInterface>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $class = $argument->getType();
        if (!$class) {
            return [];
        }
        if (!is_a($class, ModelInterface::class, true)) {
            return [];
        }
        // Basket must never be loaded via `find` method, let ActiveBasketResolver take care of it
        if (is_a($class, Basket::class, true)) {
            return [];
        }
        $entityAttribute = ReflectionAttributeReader::getAttribute($class, Entity::class);
        if (!$entityAttribute) {
            return [];
        }

        $id = (string) $request->attributes->get('id');
        $slug = (string) $request->attributes->get('slug');
        if (!$slug && !$id) {
            return [];
        }

        $repository = $this->modelManager->getRepository($entityAttribute->getRepositoryClass());

        if ($id) {
            $model = $repository->find(id: $id, await: true) ?? throw new NotFoundHttpException(sprintf('ID "%s" not found.', $id));
            yield $model;

            return;
        }

        $model = $repository->findOneBySlug($slug, true) ?? throw new NotFoundHttpException(sprintf('Slug "%s" not found.', $slug));
        yield $model;
    }
}
