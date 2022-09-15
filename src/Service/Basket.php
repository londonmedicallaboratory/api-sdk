<?php

declare(strict_types=1);

namespace LML\SDK\Service;

use RuntimeException;
use Brick\Money\Money;
use React\EventLoop\Loop;
use Brick\Math\RoundingMode;
use LML\SDK\Entity\Money\Price;
use LML\SDK\Entity\Voucher\Voucher;
use LML\SDK\Entity\Order\BasketItem;
use LML\SDK\Repository\ProductRepository;
use LML\SDK\Repository\VoucherRepository;
use LML\SDK\Entity\Product\ProductInterface;
use LML\SDK\Entity\Shipping\ShippingInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use function array_filter;
use function array_values;
use function Clue\React\Block\awaitAll;

class Basket
{
    private const SESSION_KEY = 'basket';
    private const VOUCHER_KEY = 'voucher';

    /**
     * @var null|list<BasketItem>
     */
    private ?array $items = null;

    private ?Voucher $voucher = null;

    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository,
        private VoucherRepository $voucherRepository,
    ) {
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
    }

    public function addProduct(ProductInterface $product, int $quantity): void
    {
        $item = $this->findItemOrCreateNew($product);
        $item->setQuantity($item->getQuantity() + $quantity);
    }

    /**
     * @return list<BasketItem>
     */
    public function getItems(): array
    {
        $items = $this->items ??= $this->doGetItems();
        $filtered = array_filter($items, fn(BasketItem $item) => $item->getQuantity() > 0);

        return array_values($filtered);
    }

    public function getTotal(): ?Price
    {
        $total = array_reduce($this->getItems(), fn(int $carry, BasketItem $item) => $item->getTotal()->getAmount() + $carry, 0);

        if (!$total) {
            return null;
        }

        $money = $this->applyVoucher(Money::ofMinor($total, 'GBP'));

        return Price::fromMoney($money);
    }

    public function getTotalQuantity(): int
    {
        return array_reduce($this->getItems(), fn(int $carry, BasketItem $item) => $item->getQuantity() + $carry, 0);
    }

    public function removeProduct(ProductInterface $product): void
    {
        if ($item = $this->findItem($product)) {
            $item->setQuantity(0);
        }
    }

    public function reduceQuantityForProduct(ProductInterface $product): void
    {
        $item = $this->findItemOrCreateNew($product);
        $quantity = $item->getQuantity();
        $item->setQuantity($quantity - 1);
    }

    public function incrementQuantityForProduct(ProductInterface $product): void
    {
        $item = $this->findItemOrCreateNew($product);
        $quantity = $item->getQuantity();
        $item->setQuantity($quantity + 1);
    }

    public function setQuantityForProduct(ProductInterface $product, int $quantity): void
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
        $session = $this->requestStack->getSession();
        if (!$voucher) {
            $session->remove(self::VOUCHER_KEY);
        }
        $this->voucher = $voucher;
    }

    /**
     * @return array<int, ShippingInterface>
     */
    public function getAvailableShippingMethods(): array
    {
        return array_intersect(...array_map(fn(BasketItem $basketItem) => $basketItem->getProduct()->getShippingTypes(), $this->getItems()));
    }

    private function findItem(ProductInterface $product): ?BasketItem
    {
        foreach ($this->getItems() as $item) {
            if ($product->getId() === $item->getProduct()->getId()) {
                return $item;
            }
        }

        return null;
    }

    private function findItemOrCreateNew(ProductInterface $product): BasketItem
    {
        if ($item = $this->findItem($product)) {
            return $item;
        }

        $item = new BasketItem($product, 0);
        $this->items[] = $item;

        return $item;
    }

    private function applyVoucher(Money $amount): Money
    {
        $voucher = $this->getVoucher();
        if (!$voucher) {
            return $amount;
        }

        $reducedPrice = match ($voucher->getType()) {
            'percent' => $amount->minus($amount->multipliedBy($voucher->getValue() / 100), RoundingMode::UP),
            'amount' => $amount->minus(Money::of($voucher->getValue(), 'GBP'), RoundingMode::UP),
            default => throw new RuntimeException('Unsupported voucher type'),
        };

        return $reducedPrice->getAmount()->toInt() > 0 ? $reducedPrice : Money::of(0, 'GBP');
    }

    /**
     * @return list<BasketItem>
     */
    private function doGetItems(): array
    {
        $repository = $this->productRepository;
        $session = $this->requestStack->getSession();
        /** @var array<int|string, int> $values */
        $values = $session->get(self::SESSION_KEY, []);

        $promises = [];
        foreach ($values as $id => $quantity) {
            /** @noinspection NullPointerExceptionInspection psalm takes care of this */
            $promises[] = $repository->find((string)$id)
                ->then(fn(?ProductInterface $product) => $product ? new BasketItem($product, $quantity) : null, fn() => null);
        }

        /** @var list<?BasketItem> $responses */
        $responses = awaitAll($promises, Loop::get());

        $filtered = array_filter($responses, fn(?BasketItem $item) => (bool)$item);

        return array_values($filtered);
    }

    private function doGetVoucher(): ?Voucher
    {
        $session = $this->requestStack->getSession();
        $id = (string)$session->get(self::VOUCHER_KEY);
        if (!$id) {
            return null;
        }

        return $this->voucherRepository->find($id, true);
    }
}
