<?php

declare(strict_types=1);

namespace LML\SDK\Service;

use RuntimeException;
use Brick\Money\Money;
use LML\SDK\DTO\Payment;
use React\EventLoop\Loop;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Model\Money\Price;
use LML\SDK\Model\Order\Order;
use LML\SDK\Model\Address\Address;
use LML\SDK\Model\Order\BasketItem;
use LML\SDK\Model\Customer\Customer;
use Psr\Http\Message\StreamInterface;
use LML\SDK\Repository\OrderRepository;
use LML\SDK\Model\Order\OrderInterface;
use LML\SDK\Repository\ProductRepository;
use LML\SDK\Model\Product\ProductInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use function json_decode;
use function array_filter;
use function array_values;
use function Clue\React\Block\await;
use function Clue\React\Block\awaitAll;

class Basket
{
    private const SESSION_KEY = 'basket';

    /**
     * @var null|list<BasketItem>
     */
    private ?array $items = null;

    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository,
        private OrderRepository $orderRepository,
    )
    {
    }

    public function createOrder(Payment $payment): OrderInterface
    {
        $customer = new Customer(
            id: '',
            firstName: $payment->customersFirstName ?? throw new RuntimeException(),
            lastName: $payment->customersLastName ?? throw new RuntimeException(),
            email: $payment->customersEmail ?? throw new RuntimeException(),
            phoneNumber: $payment->customersEmail ?? throw new RuntimeException(),
        );

        $address = new Address(
            id: '',
            line1: $payment->deliveryAddressLine1 ?? throw new RuntimeException(),
            postalCode: $payment->deliveryAddressLine1 ?? throw new RuntimeException(),
            countryCode: 'GB',
            line2: $payment->deliveryAddressLine1,
            line3: $payment->deliveryAddressLine1,
        );

        $order = new Order(
            id: '',
            customer: $customer,
            address: $address,
            total: $this->getTotal() ?? throw new RuntimeException(),
            items: $this->getItems(),
            companyName: $payment->customersCompany ,
            billingAddress: null,
        );

        $promise = $this->orderRepository->persist($order);
        /** @var \RingCentral\Psr7\Response $value */
        $value = await($promise, Loop::get());

        /** @var StreamInterface $stream */
        $stream = $value->getBody();
        $stringContent = (string)$stream;
        /** @var array{id: string} $data */
        $data = json_decode($stringContent, true, 512, JSON_THROW_ON_ERROR);

        $promise = $this->orderRepository->find($data['id']);
        $lazy = new LazyPromise($promise);

        return $lazy->getValue() ?? throw new RuntimeException('This order is not available.');
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
    }

    public function addProduct(ProductInterface $product, int $quantity): void
    {
        $item = $this->findItemOrCreateNew($product);
        $item->setQuantity($item->getQuantity() + $quantity);
    }

    /**
     * @return list<BasketItem>
     */
    public function getItems()
    {
        $items = $this->items ??= $this->doGetItems();
        $filtered = array_filter($items, fn(BasketItem $item) => $item->getQuantity() > 0);

        return array_values($filtered);
    }

    public function getTotal(): ?Price
    {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item->getTotal()->getAmount();
        }

        if (!$total) {
            return null;
        }

        $money = Money::ofMinor($total, 'GBP');

        return Price::fromMoney($money);
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
    private function doGetItems()
    {
        $repository = $this->productRepository;
        $session = $this->requestStack->getSession();
        /** @var array<int|string, int> $values */
        $values = $session->get(self::SESSION_KEY, []);

        $promises = [];
        foreach ($values as $id => $quantity) {
            $promises[] = $repository->find((string)$id)
                ->then(function (?ProductInterface $product) use ($quantity) {
                    return $product ? new BasketItem($product, $quantity) : null;
                });
        }

        /** @var list<?BasketItem> $responses */
        $responses = awaitAll($promises, Loop::get());

        $filtered = array_filter($responses, fn(?BasketItem $item) => (bool)$item);

        return array_values($filtered);
    }
}