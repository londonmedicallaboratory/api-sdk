<?php

declare(strict_types=1);

namespace LML\SDK\Repository\Basket;

use DateTime;
use LogicException;
use RuntimeException;
use Webmozart\Assert\Assert;
use LML\SDK\Lazy\LazyPromise;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\Basket\Basket;
use LML\SDK\Entity\Address\Address;
use LML\SDK\Entity\Basket\BasketItem;
use LML\SDK\Entity\Customer\Customer;
use LML\SDK\Repository\BrandRepository;
use LML\SDK\Repository\ProductRepository;
use LML\SDK\Repository\AddressRepository;
use LML\SDK\Repository\CustomerRepository;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Appointment\Appointment;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    private const SESSION_KEY = 'basket_id';

    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    public function findActiveOrCreate(?Customer $customer): Basket
    {
        return $this->findForCustomer($customer) ?? $this->findFromSession() ?? $this->createNew();
    }

    public function find(?string $id = null, bool $await = false, ?string $url = null): never
    {
        throw new LogicException('Use \'findActiveOrCreate\' method instead.');
    }

    protected function one($entity, $options, $optimizer): Basket
    {
        $id = $entity['id'];

        $basket = new Basket(
            id: $id,
            shippingAddress: $this->getAddress($entity['shipping_address'] ?? null),
            billingAddress: $this->getAddress($entity['billing_address'] ?? null),
            items: $this->getItems($entity['items']),
            initialAppointment: $this->getInitialAppointment($entity['initial_appointment'] ?? null),
        );

        if ($customerScalars = $entity['customer'] ?? null) {
            $customer = $this->get(CustomerRepository::class)->buildOne($customerScalars);
            $basket->setAnonCustomer($customer);
        }

        return $basket;
    }

    private function createNew(): Basket
    {
        $basket = new Basket();
        $this->persist($basket);
        $this->flush();
        $this->getSession()->set(self::SESSION_KEY, $basket->getId());

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

    private function findForCustomer(?Customer $customer): ?Basket
    {
        if (!$customer) {
            return null;
        }
        $url = sprintf('/basket/customer/%s', $customer->getId());

        if ($basket = await(parent::find(url: $url))) {
            $this->getSession()->set(self::SESSION_KEY, $basket->getId());
        }

        return $basket;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getMainRequest()?->getSession() ?? throw new RuntimeException('You must use this method from request only.');
    }

    private function findFromSession(): ?Basket
    {
        $session = $this->getSession();
        Assert::nullOrString($id = $session->get(self::SESSION_KEY));

        // @todo Discuss if basket is shared between logged and not-logged customer
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
