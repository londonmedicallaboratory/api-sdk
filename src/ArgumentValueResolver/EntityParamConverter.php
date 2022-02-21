<?php

declare(strict_types=1);

namespace LML\SDK\ArgumentValueResolver;

use RuntimeException;
use LML\SDK\Attribute\Entity;
use LML\SDK\Service\API\EntityManager;
use LML\SDK\Util\ReflectionAttributeReader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use function sprintf;

/**
 * Allows param conversion of model, similar to
 *
 * @see https://github.com/sensiolabs/SensioFrameworkExtraBundle/blob/master/src/Request/ParamConverter/DoctrineParamConverter.php
 *
 * Only slug is supported, and no conversion.
 */
class EntityParamConverter implements ParamConverterInterface
{
    public function __construct(
        private EntityManager $modelManager,
    )
    {
    }

    public function supports(ParamConverter $configuration): bool
    {
        $class = $configuration->getClass();
        if (!$class) {
            return false;
        }
        $model = ReflectionAttributeReader::getAttribute($class, Entity::class);

        return (bool)$model;
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $name = $configuration->getName();
        $id = (string)$request->attributes->get('id');
        $slug = (string)$request->attributes->get('slug');
        if (!$slug && !$id) {
            return false;
        }
        $class = $configuration->getClass() ?? throw new RuntimeException('This must never happen.');
        $attribute = ReflectionAttributeReader::getAttribute($class, Entity::class) ?? throw new RuntimeException('This must never happen.');
        $repository = $this->modelManager->getRepository($attribute->getRepositoryClass());

        if ($id) {
            $model = $repository->find($id, true) ?? throw new NotFoundHttpException(sprintf('ID "%s" not found.', $id));
            $request->attributes->set($name, $model);

            return true;
        }

        $model = $repository->findOneBySlug($slug, true) ?? throw new NotFoundHttpException(sprintf('Slug "%s" not found.', $slug));

        $request->attributes->set($name, $model);

        return true;
    }
}
