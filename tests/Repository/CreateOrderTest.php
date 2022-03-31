<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Entity\Order\Order;
use LML\SDK\Entity\Address\Address;
use LML\SDK\Entity\Customer\Customer;
use LML\SDK\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateOrderTest extends KernelTestCase
{
    public function testFindOneBy(): void
    {
        self::bootKernel();
        /** @var OrderRepository $repo */
        $repo = self::$kernel->getContainer()->get(OrderRepository::class);

//        $customer = new Customer(
//            id         : '1',
//            firstName  : 'John',
//            lastName   : 'Doe',
//            email      : 'test@example.com',
//            phoneNumber: '123123123',
//        );

//        $address = new Address(
//            id         : '2',
//            line1      : 'First line 42',
//            postalCode : 'test',
//            countryCode: 'GB',
//            countryName: 'GB',
//
//        );
//        $order = new Order(
//            id         : '1',
//            customer   : $customer,
//            address    : $address,
//            companyName: 'LML',
//        );

//        $response = $repo->persist($order);

//        dd($response->getValue());

    }

}
