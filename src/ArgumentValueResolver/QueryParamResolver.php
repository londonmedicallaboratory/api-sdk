<?php

declare(strict_types=1);

namespace LML\SDK\ArgumentValueResolver;

use DateTime;
use RuntimeException;
use LML\SDK\Attribute\QueryParam;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use function sprintf;

class QueryParamResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return (bool)$this->getAttribute($argument);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $argument->getType() ?? throw new RuntimeException('You must typehint parameter when using QueryParam attribute.');
        $attribute = $this->getAttribute($argument) ?? throw new RuntimeException('This should never happen.');

        $name = $attribute->getName();
        $value = $request->query->get($name);
        if (!$value) {
            yield $argument->isNullable() ? null : throw new RuntimeException(sprintf('Query param "%s" not found.', $name));

            return;
        }

        // add support for other types, not just the date
        yield new DateTime($value);
    }

    private function getAttribute(ArgumentMetadata $argument): ?QueryParam
    {
        $attributes = $argument->getAttributes(QueryParam::class, ArgumentMetadata::IS_INSTANCEOF);
        $first = $attributes[0] ?? null;

        return $first instanceof QueryParam ? $first : null;
    }
}
