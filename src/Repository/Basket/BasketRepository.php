<?php

declare(strict_types=1);

namespace LML\SDK\Repository\Basket;

use DateTime;
use LML\SDK\Entity\Address\Address;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Entity\Basket\Basket;
use LML\SDK\Entity\Basket\BasketItem;
use LML\SDK\Entity\Customer\Customer;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Order\Order;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Entity\Voucher\Voucher;
use LML\SDK\Exception\DataNotFoundException;
use LML\SDK\Lazy\ExtraLazyPromise;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Repository\AddressRepository;
use LML\SDK\Repository\BrandRepository;
use LML\SDK\Repository\CustomerRepository;
use LML\SDK\Repository\OrderRepository;
use LML\SDK\Repository\ProductRepository;
use LML\SDK\Repository\ShippingRepository;
use LML\SDK\Repository\VoucherRepository;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Service\Visitor\Visitor;
use LML\View\Lazy\ResolvedValue;
use LogicException;
use React\Promise\PromiseInterface;
use Webmozart\Assert\Assert;
use function array_map;
use function React\Async\await;
use function sprintf;

/**
 * @psalm-import-type S from Basket
 * @psalm-import-type S from Appointment as TAppointment
 *
 * @extends AbstractRepository<S, Basket, array>
 */
class BasketRepository extends AbstractRepository
{
    public function __construct(
        private Visitor $visitor,
    )
    {
    }

    public function getPersistenceGraph(ModelInterface $view): iterable
    {
        yield $view->getInitialAppointment();
    }

    public function findActiveOrCreate(?Customer $customer): Basket
    {
        return $this->findForCustomer($customer) ?? $this->findFromSession() ?? $this->createNew();
    }

    public function find(?string $id = null, bool $await = false, ?string $url = null, bool $force = false): never
    {
        throw new LogicException('Use \'findActiveOrCreate\' method instead.');
    }

    public function createOrder(Basket $basket): Order
    {
        $response = await($this->getClient()->patch('basket/transform_to_order', $basket->getId(), []));
        $data = (array)json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        Assert::string($orderId = $data['id'] ?? null);
        $this->createNew();

        return $this->get(OrderRepository::class)->find($orderId, true) ?? throw new LogicException('Order not found');
    }

    public function createNew(): Basket
    {
        $basket = new Basket();
        $this->persist($basket);
        $this->flush();
        $this->visitor->setBasketId($basket->getId());

        return $basket;
    }

    /**
     * @return PromiseInterface<list<Product>>
     */
    public function findAddOns(Basket $basket): PromiseInterface
    {
        $url = sprintf('/basket/%s/add-ons', $basket->getId());

        return $this->get(ProductRepository::class)->findBy(url: $url);
    }

    protected function one($entity, $options, $optimizer): Basket
    {
        $id = $entity['id'];
        $affiliateCode = $this->visitor->getAffiliateCode();

        $basket = new Basket(
            id: $id,
            transactionId: $entity['transaction_id'] ?? null,
            shippingAddress: $this->getAddress($entity['shipping_address'] ?? null),
            shipping: new LazyPromise($this->getShipping($entity['shipping_id'] ?? null)),
            billingAddress: $this->getAddress($entity['billing_address'] ?? null),
            items: $this->getItems($entity['items']),
            initialAppointment: $this->getInitialAppointment($entity['initial_appointment'] ?? null),
            affiliateCode: $affiliateCode,
            voucher: new ExtraLazyPromise(fn() => $this->getVoucher($entity['voucher_id'] ?? null))
        );

        if ($customerScalars = $entity['customer'] ?? null) {
            $customer = $this->get(CustomerRepository::class)->buildOne($customerScalars);
            $basket->setAnonCustomer($customer);
        }

        return $basket;
    }

    protected function getCacheTimeout(): ?int
    {
        return 5;
    }

    /**
     * @param ?array{
     *     line1: string,
     *     line2?: ?string,
     *     line3?: ?string,
     *     postal_code: string,
     *     country_code: string,
     *     city: string,
     *     id?: ?string,
     *     country_name?: string,
     *     state?: ?string,
     *     company?: ?string,
     * } $param
     */
    private function getAddress(?array $param): ?Address
    {
        if (!$param) {
            return null;
        }

        return $this->get(AddressRepository::class)->buildOne($param);
    }

    /**
     * @return PromiseInterface<?Shipping>
     */
    private function getShipping(?string $id): PromiseInterface
    {
        return $this->get(ShippingRepository::class)->find($id);
    }

    private function findForCustomer(?Customer $customer): ?Basket
    {
        if (!$customer) {
            return null;
        }
        $url = sprintf('/basket/customer/%s', $customer->getId());

        if ($basket = parent::find(url: $url, await: true)) {
            $this->visitor->setBasketId($basket->getId());

            return $basket;
        }

        return null;
    }

    private function findFromSession(): ?Basket
    {
        $id = $this->visitor->getBasketId();
        if (!$id) {
            return null;
        }

        // try to find by id, but command *will* create new instance if one is not found.
        // when that happens, new basket_id value must be sent to Visitor so its gets updated in cookie. Otherwise, client app will keep creating new instances
        if ($basket = parent::find(id: $id, await: true)) {
            $this->visitor->setBasketId($basket->getId());

            return $basket;
        }

        return null;
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

    /**
     * @return PromiseInterface<?Voucher>
     */
    private function getVoucher(?string $id): PromiseInterface
    {
        return $this->get(VoucherRepository::class)->find($id);
    }

    /**
     * @param ?TAppointment $initialAppointment
     */
    private function getInitialAppointment(?array $initialAppointment): ?Appointment
    {
        if (!$initialAppointment) {
            return null;
        }
        $brand = $this->get(BrandRepository::class)->fetch($initialAppointment['brand_id']);
        $location = $this->get(BrandRepository::class)->fetch($initialAppointment['test_location_id']);
        $startsAt = $initialAppointment['starts_at'] ?? throw new DataNotFoundException();

        return new Appointment(
            type: $initialAppointment['type'],
            brand: new LazyPromise($brand),
            location: new LazyPromise($location),
            startsAt: new ResolvedValue(new DateTime($startsAt)),
            timeId: new ResolvedValue($initialAppointment['time_id'] ?? null),
        );
    }
}
