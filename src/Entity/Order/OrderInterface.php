<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Order;

use DateTimeInterface;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Entity\Address\AddressInterface;
use LML\SDK\Entity\Customer\CustomerInterface;
use LML\SDK\Entity\Shipping\ShippingInterface;

/**
 * @psalm-type Item = array{product_id: string, quantity: int, product_sku?: ?string}
 *
 * @psalm-type S=array{
 *      id: string,
 *      company: ?string,
 *      shipping_id?: ?string,
 *      shipping_date?: ?string,
 *      customer_id?: ?string,
 *      address: array{
 *          id: string,
 *          city: string,
 *          country_code: string,
 *          country_name?: string,
 *          line1: string,
 *          line2?: ?string,
 *          line3?: ?string,
 *          postal_code: string
 *      },
 *      billing_address?: ?array{
 *          id: string,
 *          city: string,
 *          country_code: string,
 *          country_name?: string,
 *          line1: string,
 *          line2?: ?string,
 *          line3?: ?string,
 *          postal_code: string
 *      },
 *      customer: array{
 *          id: ?string,
 *          first_name: string,
 *          last_name: string,
 *          email: string,
 *          phone_number?: ?string,
 *          foreign_id?: ?string,
 *      },
 *      items: list<Item>,
 *      price: array{amount_minor: int, currency: string, formatted_value: string},
 *      status?: ?string,
 *      created_at?: ?string,
 *      order_number?: ?int,
 *      voucher_id?: ?string,
 * }
 *
 * @extends ModelInterface<S>
 *
 * @todo https://github.com/vimeo/psalm/issues/5148
 */
interface OrderInterface extends ModelInterface
{
    public function getShipping(): ?ShippingInterface;

    public function getCustomer(): CustomerInterface;

    public function getCompanyName(): ?string;

    public function getAddress(): AddressInterface;

    public function getBillingAddress(): ?AddressInterface;

    /**
     * @return list<ItemInterface>
     */
    public function getItems(): array;

    public function getTotal(): PriceInterface;

    public function getShippingDate(): ?DateTimeInterface;
}
