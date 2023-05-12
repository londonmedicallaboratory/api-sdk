<?php

declare(strict_types=1);

namespace LML\SDK\ArgumentValueResolver;

use LML\SDK\Attribute\ActiveBasket;
use LML\SDK\Entity\Customer\Customer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use LML\SDK\Repository\Basket\BasketRepository;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

class ActiveBasketResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private BasketRepository $basketRepository,
        private Security $security,
    )
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $attributes = $argument->getAttributes(ActiveBasket::class, ArgumentMetadata::IS_INSTANCEOF);
        $first = $attributes[0] ?? null;

        return $first instanceof ActiveBasket;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $security = $this->security;
        $customer = $security->getUser() instanceof Customer ? $security->getUser() : null;

        yield $this->basketRepository->findActiveOrCreate($customer);
    }
}