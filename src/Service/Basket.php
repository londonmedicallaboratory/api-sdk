<?php

declare(strict_types=1);

namespace LML\SDK\Service;

use RuntimeException;
use Brick\Money\Money;
use LML\SDK\Entity\Money\Price;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\Voucher\Voucher;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Entity\Order\OrderItem;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Repository\ProductRepository;
use LML\SDK\Repository\VoucherRepository;
use LML\SDK\Repository\ShippingRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use function array_filter;
use function array_values;
use function React\Async\await;
use function React\Async\awaitAll;
use function React\Async\parallel;

class Basket
{
    private const SESSION_KEY = 'basket';
    private const VOUCHER_KEY = 'voucher';
    private const SHIPPING_KEY = 'shipping';

    /**
     * @var null|list<OrderItem>
     */
    private ?array $items = null;

    private ?Voucher $voucher = null;

    private ?Shipping $shipping = null;

    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository,
        private VoucherRepository $voucherRepository,
        private ShippingRepository $shippingRepository,
    )
    {
    }

    public function empty(): void
    {
        $this->items = [];
        $this->save();
    }

    public function save(): void
    {
        $session = $this->requestStack->getSession();
        $data = [];
        foreach ($this->getItems() as $item) {
            $quantity = $item->getQuantity();
            if ($quantity >= 1) {
                $data[$item->getProduct()->getId()] = $quantity;
            }
        }
        $session->set(self::SESSION_KEY, $data);
        if ($this->voucher) {
            $session->set(self::VOUCHER_KEY, $this->voucher->getId());
        }
        if ($this->shipping) {
            $session->set(self::SHIPPING_KEY, $this->shipping->getId());
        }
    }

    public function addProduct(Product $product, int $quantity): void
    {
        $item = $this->findItemOrCreateNew($product);
        $item->setQuantity($item->getQuantity() + $quantity);
    }

    /**
     * @return list<OrderItem<Product>>
     */
    public function getItems(): array
    {
        $items = $this->items ??= $this->doGetItems();
        $filtered = array_filter($items, fn(OrderItem $item) => $item->getQuantity() > 0);

        return array_values($filtered);
    }

    public function getSubtotal(): ?PriceInterface
    {
        $total = array_reduce($this->getItems(), fn(int $carry, OrderItem $item) => $item->getTotal()->getAmount() + $carry, 0);
        if (!$total) {
            return null;
        }

        return Price::fromMoney(Money::ofMinor($total, 'GBP'));
    }

    public function getTotal(): ?PriceInterface
    {
        if (!$subtotal = $this->getSubtotal()) {
            return null;
        }
        $newPrice = $this->applyVoucher($subtotal);

        return $this->getShipping() ? $newPrice->plus($this->getShipping()->getPrice()) : $newPrice;
    }

    public function getTotalQuantity(): int
    {
        return array_reduce($this->getItems(), fn(int $carry, OrderItem $item) => $item->getQuantity() + $carry, 0);
    }

    public function removeProduct(Product $product): void
    {
        if ($item = $this->findItem($product)) {
            $item->setQuantity(0);
        }
    }

    public function reduceQuantityForProduct(Product $product): void
    {
        $item = $this->findItemOrCreateNew($product);
        $quantity = $item->getQuantity();
        $item->setQuantity($quantity - 1);
    }

    public function incrementQuantityForProduct(Product $product): void
    {
        $item = $this->findItemOrCreateNew($product);
        $quantity = $item->getQuantity();
        $item->setQuantity($quantity + 1);
    }

    public function setQuantityForProduct(Product $product, int $quantity): void
    {
        $item = $this->findItemOrCreateNew($product);
        $item->setQuantity($quantity);
    }

    public function getVoucher(): ?Voucher
    {
        return $this->voucher ??= $this->doGetVoucher();
    }

    public function setVoucher(?Voucher $voucher): void
    {
        if (!$voucher) {
            $session = $this->requestStack->getSession();
            $session->remove(self::VOUCHER_KEY);
        }
        $this->voucher = $voucher;
    }

    /**
     * @return array<int, Shipping>
     */
    public function getAvailableShippingMethods(): array
    {
        $itemsShippingMethods = array_map(fn(OrderItem $basketItem) => $basketItem->getProduct()->getShippingTypes(), $this->getItems());
        foreach ($itemsShippingMethods as $itemShippingMethods) {
            if (!empty($itemShippingMethods)) {
                return array_intersect(...$itemsShippingMethods);
            }
        }

        return [];
    }

    public function getDiscount(): ?PriceInterface
    {
        $voucher = $this->getVoucher();
        $subtotalAmount = $this->getSubtotal()?->getAmount();
        if (!$voucher || !$subtotalAmount) {
            return null;
        }

        $discountAMount = match ($voucher->getType()) {
            'percent' => $subtotalAmount * ($voucher->getValue() / 100),
            'amount' => $voucher->getValue() * 100,
            default => throw new RuntimeException('Unsupported voucher type'),
        };

        return Price::fromMoney(Money::ofMinor($discountAMount, 'GBP'));
    }

    public function getShipping(): ?Shipping
    {
        return $this->shipping ??= $this->doGetShipping();
    }

    public function setShipping(?Shipping $shipping): void
    {
        $this->shipping = $shipping;
        if (!$shipping) {
            $session = $this->requestStack->getSession();
            $session->remove(self::SHIPPING_KEY);
        }
    }

    private function findItem(Product $product): ?OrderItem
    {
        foreach ($this->getItems() as $item) {
            if ($product->getId() === $item->getProduct()->getId()) {
                return $item;
            }
        }

        return null;
    }

    private function findItemOrCreateNew(Product $product): OrderItem
    {
        if ($item = $this->findItem($product)) {
            return $item;
        }

        $item = new OrderItem(new ResolvedValue($product), 0);
        $this->items[] = $item;

        return $item;
    }

    private function applyVoucher(PriceInterface $price): PriceInterface
    {
        $discount = $this->getDiscount();
        if (!$discount) {
            return $price;
        }

        return $price->getAmount() < $discount->getAmount() ? Price::fromMoney(Money::ofMinor(0, 'GBP')) : $price->minus($discount);
    }

    /**
     * @return list<OrderItem<Product>>
     */
    private function doGetItems(): array
    {
        $repository = $this->productRepository;
        $session = $this->requestStack->getSession();
        /** @var array<int|string, int> $values */
        $values = $session->get(self::SESSION_KEY, []);

        $tasks = [];
        foreach ($values as $id => $quantity) {
            $tasks[] = fn(): PromiseInterface => $this->createTask((string)$id, $quantity);
        }
        $parallel = parallel($tasks);
        $responses = await($parallel);

        $filtered = array_filter($responses, fn(?OrderItem $item) => (bool)$item);

        return array_values($filtered);
    }

    /**
     * @return PromiseInterface<null|OrderItem<Product>>
     */
    private function createTask(string $id, int $quantity): PromiseInterface
    {
        $repository = $this->productRepository;
        $promise = $repository->find($id);
        /** @noinspection NullPointerExceptionInspection - EA doesn't understand psalm */
        return $promise->then(fn(?Product $product) => $product ? new OrderItem(new ResolvedValue($product), $quantity) : null, fn() => null);
    }

    private function doGetVoucher(): ?Voucher
    {
        $session = $this->requestStack->getSession();
        $id = (string)$session->get(self::VOUCHER_KEY);
        if (!$id) {
            return null;
        }

        return $this->voucherRepository->find(id: $id, await: true);
    }

    private function doGetShipping(): ?Shipping
    {
        $session = $this->requestStack->getSession();
        $id = (string)$session->get(self::SHIPPING_KEY);
        if (!$id) {
            return null;
        }

        return $this->shippingRepository->find($id, await: true);
    }
}
