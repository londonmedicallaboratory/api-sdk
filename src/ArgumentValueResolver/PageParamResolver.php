<?php

declare(strict_types=1);

namespace LML\SDK\ArgumentValueResolver;

use LML\SDK\Attribute\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

class PageParamResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $attributes = $argument->getAttributes(Page::class, ArgumentMetadata::IS_INSTANCEOF);
        $first = $attributes[0] ?? null;

        return $first instanceof Page;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $name = $argument->getName();

        yield $request->query->getInt($name, 1);
    }
}
