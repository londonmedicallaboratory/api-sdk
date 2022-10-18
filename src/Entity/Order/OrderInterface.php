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
 * @psalm-import-type S from AddressInterface as TAddress
 * @psalm-import-type S from CustomerInterface as TCustomer
 *
 * @psalm-type S=array{
 *      id: string,
 *      company: ?string,
 *      shipping_id?: ?string,
 *      shipping_date?: ?string,
 *      customer_id?: ?string,
 *      address: TAddress,
 *      billing_address?: ?TAddress,
 *      customer: TCustomer,
 *      items: list<Item>,
 *      price: array{amount_minor: int, currency: string, formatted_value: string},
 *      status?: ?string,
 *      created_at?: ?string,
 *      order_number?: ?int,
 *      voucher_id?: ?string,
 * }
 *
 * @extends ModelInterface<S>
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
