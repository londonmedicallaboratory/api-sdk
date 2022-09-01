<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use React\EventLoop\Loop;
use LML\SDK\Entity\Customer\Customer;
use React\Http\Message\ResponseException;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Customer\CustomerInterface;
use function Clue\React\Block\await;

/**
 * @psalm-import-type S from CustomerInterface
 *
 * @extends AbstractRepository<S, Customer, array{product_id?: string}>
 */
class CustomerRepository extends AbstractRepository
{
    public function login(string $email, string $password): ?Customer
    {
        try {
            $promise = $this->getClient()->post('/customer/auth', [
                'email'    => $email,
                'password' => $password,
            ]);
            $response = await($promise, Loop::get());
            $body = (string)$response->getBody();
            $data = (array)json_decode($body, false, 512, JSON_THROW_ON_ERROR);

            /** @psalm-suppress MixedArgumentTypeCoercion */
            return $this->buildOne($data);
        } catch (ResponseException) {
            return null;
        }
    }

    protected function one($entity, $options, $optimizer): Customer
    {
        $id = $entity['id'];

        return new Customer(
            id         : $id,
            firstName  : $entity['first_name'],
            lastName   : $entity['last_name'],
            email      : $entity['email'],
            phoneNumber: $entity['phone_number'] ?? null,
            foreignId  : $entity['foreign_id'] ?? null,
        );
    }
}
