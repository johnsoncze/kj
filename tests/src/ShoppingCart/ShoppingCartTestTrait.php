<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart;

use App\Location\State;
use App\ShoppingCart\ShoppingCart;
use App\ShoppingCart\ShoppingCartHash;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ShoppingCartTestTrait
{


    /**
     * @return ShoppingCart
     */
    private function createTestShoppingCart() : ShoppingCart
    {
        $cart = new ShoppingCart();
        $cart->setId(1);
		$cart->setName('John');
		$cart->setFirstName($cart->getName());
		$cart->setLastName('Doe');
		$cart->setEmail('johndoe@jk.cz');
		$cart->setTelephone('+420773911088');

        //billing data
		$cart->setBillingAddress('Billing street 1');
		$cart->setBillingCity('Billing city');
		$cart->setBillingCountry(State::CZ);
		$cart->setBillingPostalCode(56789);
		$cart->setBillingEmail($cart->getEmail());
		$cart->setBillingTelephone($cart->getTelephone());

        //delivery data
		$cart->setDeliveryFirstName('John');
		$cart->setDeliveryLastName('Doe');
		$cart->setDeliveryCompany('Company name');
		$cart->setDeliveryAddress('Delivery street 1');
		$cart->setDeliveryCity('Delivery city');
		$cart->setDeliveryCountry(State::CZ);
		$cart->setDeliveryPostalCode(12345);

        $cart->setHash(ShoppingCartHash::generateHash());

        return $cart;
    }
}