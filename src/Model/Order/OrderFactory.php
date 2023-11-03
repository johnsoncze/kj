<?php

declare(strict_types = 1);

namespace App\Order;

use App\Customer\Customer;
use App\Helpers\Prices;
use App\ShoppingCart\ShoppingCartDTO;


class OrderFactory
{


    /** @var OrderCode */
    protected $orderCode;



    public function __construct(OrderCode $orderCode)
    {
        $this->orderCode = $orderCode;
    }



    /**
     * @param $customer Customer|null
     * @param $cartDTO ShoppingCartDTO
     * @return Order
     * @throws \InvalidArgumentException
     * @throws \EntityInvalidArgumentException
     */
    public function createByShoppingCart(Customer $customer = NULL,
                                         ShoppingCartDTO $cartDTO) : Order
    {
        $cart = $cartDTO->getEntity();
        $cartDelivery = $cartDTO->getDelivery();
        $cartPayment = $cartDTO->getPayment();
        $delivery = $cartDelivery->getCatalogDelivery();
        $payment = $cartPayment->getCatalogPayment();
        $price = $cartDTO->getPrice();

        $order = new Order();
        $order->setCode($this->orderCode->generate());

        $order->setCustomerFirstName($cart->getFirstName());
        $order->setCustomerLastName($cart->getLastName());
        $order->setCustomerEmail($cart->getBillingEmail());
        $order->setCustomerTelephone($cart->getBillingTelephone());
        $order->setBillingAddressStreet($cart->getBillingAddress());
        $order->setBillingAddressCity($cart->getBillingCity());
        $order->setBillingAddressPostcode($cart->getBillingPostalCode());
        $order->setBillingAddressCountry($cart->getBillingCountry());

        $order->setDeliveryAddressFirstName($cart->getDeliveryFirstName());
        $order->setDeliveryAddressLastName($cart->getDeliveryLastName());
        $order->setDeliveryAddressCompany($cart->getDeliveryCompany());
        $order->setDeliveryAddressStreet($cart->getDeliveryAddress());
        $order->setDeliveryAddressCity($cart->getDeliveryCity());
        $order->setDeliveryAddressPostcode($cart->getDeliveryPostalCode());
        $order->setDeliveryAddressCountry($cart->getDeliveryCountry());
        $order->setDeliveryTelephone($cart->getTelephone());

        $order->setPaymentVat($cartPayment->getVat());
        $order->setPaymentPrice($cartPayment->getPrice());
        $order->setPaymentName($cartPayment->getTranslatedName());
        $order->setPaymentId($payment->getId());
        $order->setPaymentExternalSystemId($payment->getExternalSystemId());
        $order->setIsRequiredPaymentGateway($payment->isRequiredPaymentGateway());
        $order->setTransferPayment($payment->isTransfer());

        $order->setDeliveryVat($cartDelivery->getVat());
        $order->setDeliveryPrice($cartDelivery->getPrice());
        $order->setDeliveryName($cartDelivery->getTranslatedName());
        $order->setDeliveryId($delivery->getId());
        $order->setDeliveryExternalSystemId($delivery->getExternalSystemId());
        $order->setDeliveryInformation($cart->getDeliveryInformation());

        //todo add delivery and payment to summary price
        $order->setSummaryPrice($price->summaryPrice);
        $order->setSummaryPriceWithoutVat($price->summaryPriceWithoutVat);
        $order->setSummaryPriceBeforeDiscount($price->summaryPriceBeforeDiscount);
        $order->setSummaryPriceBeforeDiscountWithoutVat($price->summaryPriceBeforeDiscountWithoutVat);
        $order->setProductSummaryPriceWithoutVat($order->countProductSummaryPriceWithoutVat());
        $order->setProductSummaryPrice($order->countProductSummaryPrice());

        $order->setBirthdayDiscount($cart->isAppliedBirthdayCoupon());
        $order->setComment($cart->getComment());
        $order->setState(Order::NEW_STATE);

        if ($customer) {
            $order->setCustomerId($customer->getId());
            $order->setCustomerExternalSystemId($customer->getExternalSystemId());
        }

        return $order;
    }
}