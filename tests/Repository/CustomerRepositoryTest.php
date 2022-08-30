<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use DateTime;
use LogicException;
use InvalidArgumentException;
use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\Customer\Customer;
use LML\SDK\Repository\CustomerRepository;

class CustomerRepositoryTest extends AbstractTest
{
    private static ?string $randomEmail = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$randomEmail = sprintf('test-%s@example.com', (new DateTime())->format('U'));
    }

    public function testCreate(): string
    {
        self::bootKernel();
        $repo = $this->getCustomerRepository();

        $customer = new Customer(
            firstName: 'Test',
            lastName : 'Test',
            email    : $this->getRandomEmail(),
        );
        $repo->persist($customer);
        $repo->flush();

        $id = $customer->getId();
        self::assertNotNull($id);

        $repo->clear();

        return $id;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(string $id): string
    {
        self::bootKernel();
        $repo = $this->getCustomerRepository();
        $customer = $this->getTestCustomer($id);

        $customer->setFirstName('test 2');
        $customer->setLastName('test 3');

        $repo->flush();
        $repo->clear();

        $customer = $repo->find($id, await: true) ?? throw new InvalidArgumentException('Something went wrong.');

        self::assertEquals('test 2', $customer->getFirstName());
        self::assertEquals('test 3', $customer->getLastName());

        return $id;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(string $id): void
    {
        self::bootKernel();
        $repo = $this->getCustomerRepository();
        $customer = $this->getTestCustomer($id);

        $repo->remove($customer);
        $repo->flush();
    }

    private function getTestCustomer(string $id): Customer
    {
        $repo = $this->getCustomerRepository();
        $customer = $repo->find($id, await: true);
        self::assertInstanceOf(Customer::class, $customer);

        return $customer;
    }

    private function getRandomEmail(): string
    {
        return self::$randomEmail ?? throw new LogicException('\'setUpBeforeClass\' method must create unique email.');
    }

    private function getCustomerRepository(): CustomerRepository
    {
        $repo = $this->getService(CustomerRepository::class);
        $repo->clear();

        return $repo;
    }
}
