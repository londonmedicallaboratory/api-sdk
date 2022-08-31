<?php

declare(strict_types=1);

namespace LML\SDK\Service;

use RuntimeException;
use Brick\Money\Money;
use React\EventLoop\Loop;
use LML\SDK\Entity\Money\Price;
use LML\SDK\Entity\Voucher\Voucher;
use LML\SDK\Entity\Order\BasketItem;
use LML\SDK\Repository\OrderRepository;
use LML\SDK\Repository\ProductRepository;
use LML\SDK\Repository\VoucherRepository;
use LML\SDK\Repository\ShippingRepository;
use LML\SDK\Entity\Product\ProductInterface;
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
        private OrderRepository $orderRepository,
        private VoucherRepository $voucherRepository,
        private ShippingRepository $shippingRepository,
    ) {
    }

//
//    /**
//     * @todo Refactor to use OrderRepository::create()
//     */
//    public function createOrder(Payment $payment): OrderInterface
//    {
//        $customer = new Customer(
//            id         : '',
//            firstName  : $payment->customersFirstName ?? throw new RuntimeException(),
//            lastName   : $payment->customersLastName ?? throw new RuntimeException(),
//            email      : $payment->customersEmail ?? throw new RuntimeException(),
//            phoneNumber: $payment->customersPhoneNumber ?? throw new RuntimeException(),
//        );
//
//        $deliveryLine1 = $payment->deliveryAddressLine1 ?? $payment->customersAddressLine1;
//        $postalCode = $payment->deliveryPostalCode ?? $payment->customersPostalCode;
//
//        $address = new Address(
//            id         : '',
//            line1      : $deliveryLine1 ?? throw new RuntimeException(),
//            postalCode : $postalCode ?? throw new RuntimeException(),
//            city       : '',
//            countryCode: 'GB',
//            countryName: 'GB',
//            line2      : $payment->deliveryAddressLine2 ?? $payment->customersAddressLine2,
//            line3      : $payment->deliveryAddressLine3 ?? $payment->customersAddressLine3,
//        );
//
//        $order = new Order(
//            id            : '',
//            customer      : new ResolvedValue($customer),
//            address       : new ResolvedValue($address),
//            total         : $this->getTotal() ?? throw new RuntimeException(),
//            items         : new LazyValue(fn() => $this->getItems()),
//            companyName   : $payment->customersCompany,
//            billingAddress: null,
//            shipping: new ResolvedValue(null),
//        );
//
//        $promise = $this->orderRepository->persist($order);
//        /** @var Response $value */
//        $value = await($promise, Loop::get());
//
//        /** @var StreamInterface $stream */
//        $stream = $value->getBody();
//        $stringContent = (string)$stream;
//        /** @var array{id: string} $data */
//        $data = json_decode($stringContent, true, 512, JSON_THROW_ON_ERROR);
//
//        $promise = $this->orderRepository->find($data['id']);
//        $lazy = new LazyPromise($promise);
//
//        return $lazy->getValue() ?? throw new RuntimeException('This order is not available.');
//    }

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
            $session->set(self::VOUCHER_KEY, $this->voucher->getCode());
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

    private function applyVoucher(Money $total): Money
    {
        if (!$voucher = $this->getVoucher()) {
            return $total;
        }

        if ($voucher->getType() === 'percent') {
            return $total->minus($total->multipliedBy($voucher->getValue() / 100));
        }

        if ($voucher->getType() === 'amount') {
            return $total->minus(Money::of($voucher->getValue(), 'GBP'));
        }

        throw new RuntimeException('Unsupported voucher type');
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
