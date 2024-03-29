<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\Address\Address;
use LML\SDK\Entity\Customer\Customer;
use LML\View\Lazy\LazyValueInterface;
use React\Http\Message\ResponseException;
use LML\SDK\Service\API\AbstractRepository;
use function React\Async\await;

/**
 * @psalm-import-type S from Customer
 *
 * @extends AbstractRepository<S, Customer, array{product_id?: string}>
 */
class CustomerRepository extends AbstractRepository
{
    public function login(string $email, string $password): ?Customer
    {
        try {
            $promise = $this->getClient()->post('/customer/auth', [
                'email' => $email,
                'password' => $password,
            ]);
            $response = await($promise);
            $body = (string)$response->getBody();
            $data = (array)json_decode($body, false, 512, JSON_THROW_ON_ERROR);

            /** @psalm-suppress MixedArgumentTypeCoercion */
            return $this->buildOne($data);
        } catch (ResponseException) {
            return null;
        }
    }

    public function setPassword(string $password, Customer $customer): void
    {
        $this->getClient()->patch('/customer', $customer->getId(), ['password' => $password]);
    }

    protected function one($entity, $options, $optimizer): Customer
    {
        $id = $entity['id'] ?? null;

        return new Customer(
            id: $id,
            firstName: $entity['first_name'],
            lastName: $entity['last_name'],
            billingAddress: $this->getBillingAddress($entity),
            email: $entity['email'],
            passwordSet: $entity['password_set'] ?? false,
            phoneNumber: $entity['phone_number'] ?? null,
            foreignId: $entity['foreign_id'] ?? null,
            isSubscribedToNewsletter: new ResolvedValue($entity['is_subscribed_to_newsletter'] ?? false),
        );
    }

    /**
     * @param S $entity
     *
     * @return LazyValueInterface<?Address>
     */
    private function getBillingAddress(array $entity): LazyValueInterface
    {
        if (!$id = $entity['id'] ?? null) {
            return new ResolvedValue(null);
        }
        if (!isset($entity['billing_address_id'])) {
            return new ResolvedValue(null);
        }

        $url = sprintf('/customer/%s/billing_address', $id);
        $promise = $this->get(AddressRepository::class)->findOneBy(url: $url);

        return new LazyPromise($promise);
    }
}
