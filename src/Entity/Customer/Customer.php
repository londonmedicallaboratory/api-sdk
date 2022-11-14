<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Customer;

use Stringable;
use LML\SDK\Attribute\Entity;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Address\Address;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\CustomerRepository;
use LML\SDK\Exception\EntityNotPersistedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use function sprintf;

/**
 * @see CustomerRepository::one()
 *
 * @psalm-type S=array{
 *      id: ?string,
 *      first_name: string,
 *      last_name: string,
 *      email: string,
 *      phone_number?: ?string,
 *      foreign_id?: ?string,
 *      password?: string,
 *      billing_address_id?: ?string,
 *      is_subscribed_to_newsletter?: bool,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: CustomerRepository::class, baseUrl: 'customer')]
class Customer implements ModelInterface, Stringable, UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @param LazyValueInterface<bool> $isSubscribedToNewsletter
     * @param ?LazyValueInterface<?Address> $billingAddress
     */
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $email,
        protected LazyValueInterface $isSubscribedToNewsletter,
        private ?string $phoneNumber = null,
        private ?string $foreignId = null,
        private ?string $id = null,
        private ?string $password = null,
        protected ?LazyValueInterface $billingAddress = null,
    )
    {
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getId(): string
    {
        return $this->id ?? throw new EntityNotPersistedException();
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    public function getRoles(): array
    {
        return ['ROLE_CUSTOMER', 'ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress?->getValue();
    }

    public function setBillingAddress(?Address $billingAddress): void
    {
        $this->billingAddress = new ResolvedValue($billingAddress);
    }

    public function isSubscribedToNewsletter(): bool
    {
        return $this->isSubscribedToNewsletter->getValue();
    }

    public function setIsSubscribedToNewsletter(bool $isSubscribedToNewsletter): void
    {
        $this->isSubscribedToNewsletter = new ResolvedValue($isSubscribedToNewsletter);
    }

    public function getForeignId(): ?string
    {
        return $this->foreignId;
    }

    public function toArray()
    {
        $data = [
            'id' => $this->id,
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'phone_number' => $this->getPhoneNumber(),
            'email' => $this->getEmail(),
            'foreign_id' => $this->foreignId,
            'is_subscribed_to_newsletter' => $this->isSubscribedToNewsletter(),
        ];
        if (!$this->id && $password = $this->getPassword()) {
            $data['password'] = $password;
        }

        return $data;
    }
}
