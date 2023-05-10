<?php

declare(strict_types=1);

namespace LML\SDK\Repository\Basket;

use RuntimeException;
use Webmozart\Assert\Assert;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\Basket\Basket;
use LML\SDK\Entity\Basket\BasketItem;
use LML\SDK\Repository\ProductRepository;
use LML\SDK\Service\API\AbstractRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use function array_map;

/**
 * @psalm-import-type S from Basket
 *
 * @extends AbstractRepository<S, Basket, array>
 */
class BasketRepository extends AbstractRepository
{
    private const SESSION_KEY = 'basket_id';

    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    public function findActiveOrCreate(): Basket
    {
        $session = $this->requestStack->getMainRequest()?->getSession() ?? throw new RuntimeException('You must use this method from request only.');
        Assert::nullOrString($id = $session->get(self::SESSION_KEY));
        
        if ($basket = $this->find($id, await: true)) {
            return $basket;
        }

        $basket = new Basket();
        $this->persist($basket);
        $this->flush();
        $session->set(self::SESSION_KEY, $basket->getId());

        return $basket;
    }

    protected function one($entity, $options, $optimizer): Basket
    {
        $id = $entity['id'];

        return new Basket(
            id: $id,
            items: $this->getItems($entity['items']),
        );
    }

    /**
     * @param list<array{product_id: string, quantity: int}> $items
     *
     * @return list<BasketItem>
     */
    private function getItems(array $items): array
    {
        return array_map(function ($item) {
            $product = $this->get(ProductRepository::class)->fetch(id: $item['product_id']);

            return new BasketItem(
                product: new LazyPromise($product),
                quantity: $item['quantity'],
            );
        }, $items);
    }
}
