<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Repository\OrderRepository;

class CreateOrderTest extends AbstractTest
{
    public function testFindOneBy(): void
    {
        self::bootKernel();
        $repo = $this->getService(OrderRepository::class);

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
