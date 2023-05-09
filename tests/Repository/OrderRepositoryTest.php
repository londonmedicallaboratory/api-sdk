<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use DateTime;
use InvalidArgumentException;
use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\Order\Order;
use LML\SDK\Entity\Money\Price;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Enum\OrderStatusEnum;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Entity\Order\BasketItem;
use LML\SDK\Exception\FlushException;
use LML\SDK\Repository\OrderRepository;
use LML\SDK\Repository\BrandRepository;
use LML\SDK\Repository\ProductRepository;
use LML\SDK\Repository\CustomerRepository;
use LML\SDK\Repository\ShippingRepository;
use LML\SDK\Entity\Appointment\Appointment;
use function array_map;

class OrderRepositoryTest extends AbstractTest
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getOrderRepository()->paginate(await: true);
        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
    }

    public function testPreventPersistedAppointments(): void
    {
        self::bootKernel();
        $this->expectException(FlushException::class);
        $repo = $this->getOrderRepository();

        $brand = $this->getService(BrandRepository::class)->findOneBy(await: true) ?? throw new InvalidArgumentException('No brand found.');
        $customer = $this->getService(CustomerRepository::class)->findOneBy(await: true) ?? throw new InvalidArgumentException('No customer found.');
        $products = $this->getService(ProductRepository::class)->findAll(await: true);
        $items = array_map(fn(Product $product) => new BasketItem(new ResolvedValue($product), 10), $products);
        $shipping = $this->getService(ShippingRepository::class)->findOneBy(await: true) ?? throw new InvalidArgumentException('No shipping found.');

        $appointment = new Appointment(
            brand: new ResolvedValue($brand),
            appointmentTime: new ResolvedValue(DateTime::createFromFormat('Y-m-d H:i', '2024-01-01 15:00')),
            product: new ResolvedValue(null),
            patient: new ResolvedValue(null),
            isConfirmed: new ResolvedValue(false),
        );
        $repo->persist($appointment);

        $order = new Order(
            id: '',
            customer: new ResolvedValue($customer),
            address: new ResolvedValue(null),
            total: new Price(10000, 'GBP', '100 GBP'),
            items: new ResolvedValue($items),
            shipping: new ResolvedValue($shipping),
            appointments: new ResolvedValue([]),
            billingAddress: new ResolvedValue(null),
            status: OrderStatusEnum::AWAITING_PAYMENT,
            initialAppointment: $appointment,
        );

        $repo->persist($order);
        $repo->flush();
    }

    private function getOrderRepository(): OrderRepository
    {
        return $this->getService(OrderRepository::class);
    }
}
