<?php

declare(strict_types=1);

namespace LML\SDK\Repository\Basket;

use DateTime;
use LogicException;
use Webmozart\Assert\Assert;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\Order\Order;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\Basket\Basket;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Address\Address;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Voucher\Voucher;
use LML\SDK\Service\Visitor\Visitor;
use LML\SDK\Entity\Basket\BasketItem;
use LML\SDK\Entity\Customer\Customer;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Repository\BrandRepository;
use LML\SDK\Repository\OrderRepository;
use LML\SDK\Repository\ProductRepository;
use LML\SDK\Repository\AddressRepository;
use LML\SDK\Repository\VoucherRepository;
use LML\SDK\Repository\CustomerRepository;
use LML\SDK\Repository\ShippingRepository;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Appointment\Appointment;
use function sprintf;
use function array_map;
use function Clue\React\Block\await;

/**
 * @psalm-import-type S from Basket
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

    public function find(?string $id = null, bool $await = false, ?string $url = null): never
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

    protected function one($entity, $options, $optimizer): Basket
    {
        $id = $entity['id'];
        $affiliateCode = $this->visitor->getAffiliateCode();

        $basket = new Basket(
            id: $id,
            transactionId: $entity['transaction_id'] ?? null,
            shippingAddress: $this->getAddress($entity['shipping_address'] ?? null),
            shipping: $this->getShipping($entity['shipping_id'] ?? null),
            billingAddress: $this->getAddress($entity['billing_address'] ?? null),
            items: $this->getItems($entity['items']),
            initialAppointment: $this->getInitialAppointment($entity['initial_appointment'] ?? null),
            affiliateCode: $affiliateCode,
            voucher: new LazyPromise($this->getVoucher($entity['voucher_id'] ?? null))
        );

        if ($customerScalars = $entity['customer'] ?? null) {
            $customer = $this->get(CustomerRepository::class)->buildOne($customerScalars);
            $basket->setAnonCustomer($customer);
        }

        return $basket;
    }

    protected function getCacheTimeout(): ?int
    {
        return 2;
    }

    private function createNew(): Basket
    {
        $basket = new Basket();
        $this->persist($basket);
        $this->flush();
        $this->visitor->setBasketId($basket->getId());

        return $basket;
    }

    /**
     * @param ?array{
     *     line1: string,
     *     line2?: ?string,
     *     line3?: ?string,
     *     postal_code: string,
     *     country_code: string,
     *     city: string,
     * } $param
     */
    private function getAddress(?array $param): ?Address
    {
        if (!$param) {
            return null;
        }

        return $this->get(AddressRepository::class)->buildOne($param);
    }

    private function getShipping(?string $id): ?Shipping
    {
        return $this->get(ShippingRepository::class)->find($id, true);
    }

    private function findForCustomer(?Customer $customer): ?Basket
    {
        if (!$customer) {
            return null;
        }
        $url = sprintf('/basket/customer/%s', $customer->getId());

        if ($basket = await(parent::find(url: $url))) {
            $this->visitor->setBasketId($basket->getId());
        }

        return $basket;
    }

    private function findFromSession(): ?Basket
    {
        $id = $this->visitor->getBasketId();

        return parent::find(id: $id, await: true);
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
     * @param ?array{brand_id: string, appointment_time: string} $initialAppointment
     */
    private function getInitialAppointment(?array $initialAppointment): ?Appointment
    {
        if (!$initialAppointment) {
            return null;
        }
        $brand = $this->get(BrandRepository::class)->fetch($initialAppointment['brand_id']);

        return new Appointment(
            brand: new LazyPromise($brand),
            appointmentTime: new ResolvedValue(new DateTime($initialAppointment['appointment_time'])),
        );
    }
}
