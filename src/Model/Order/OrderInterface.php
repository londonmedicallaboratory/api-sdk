<?php

declare(strict_types=1);

namespace LML\SDK\Model\Order;

use LML\SDK\Model\ModelInterface;
use LML\SDK\Model\Money\PriceInterface;
use LML\SDK\Model\Address\AddressInterface;
use LML\SDK\Model\Customer\CustomerInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      company: ?string,
 *      address: array{
 *          id: string,
 *          city: string,
 *          country_code: string,
 *          country_name: string,
 *          line1: string,
 *          line2: ?string,
 *          line3: ?string,
 *          postal_code: string
 *      },
 *      customer: array{
 *          id: string,
 *          email: string,
 *          first_name: string,
 *          last_name: string,
 *          phone_number: string
 *      },
 *      items: list<array{product_id: string, quantity: int}>,
 *      price: array{amount_minor: int, currency: string, formatted_value: string},
 * }
 *
 * @extends ModelInterface<S>
 *
 * @todo https://github.com/vimeo/psalm/issues/5148
 */
interface OrderInterface extends ModelInterface
{
    public function getCustomer(): CustomerInterface;

    public function getCompanyName(): ?string;

    public function getAddress(): AddressInterface;

    public function getBillingAddress(): ?AddressInterface;

    /**
     * @return list<ItemInterface>
     */
    public function getItems();

    public function getTotal(): PriceInterface;
}
