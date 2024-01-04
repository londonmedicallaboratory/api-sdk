<?php

declare(strict_types=1);

namespace LML\SDK\ArgumentValueResolver;

use LML\SDK\Attribute\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class PageParamResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($argument)) {
            return [];
        }
        $name = $argument->getName();

        yield $request->query->getInt($name, 1);
    }

    private function supports(ArgumentMetadata $argument): bool
    {
        $attributes = $argument->getAttributes(Page::class, ArgumentMetadata::IS_INSTANCEOF);
        $first = $attributes[0] ?? null;

        return $first instanceof Page;
    }
}
