<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart;

use App\ShoppingCart\ShoppingCart;
use App\ShoppingCart\ShoppingCartHash;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartHashTest extends BaseTestCase
{


    public function testSetHash()
    {
        $id = 5;
        $ipAddress = '123.456.789';
        $name = 'Dušan Mlynarčík';
        $email = 'dusan.mlynarcik@email.cz';
        $telephone = '+420773911088';
        $deliveryAddress = 'Václavské náměstí 1, Praha 1 - Nové Město';
        $deliveryCity = 'Praha';
        $deliveryPostalCode = '110 00';
        $deliveryCountry = 'Česká republika';
        $billingName = 'Název firmy s.r.o.';
        $billingAddress = 'Václavské náměstí 12/157, Praha 1 - Nové Město';
        $billingCity = 'Praha';
        $billingPostalCode = '110 00';
        $billingCountry = 'Česká republika';
        $billingIn = '01411136';
        $billingVatId = 'CZ01411136';
        $billingTelephone = '+420773911088';
        $billingEmail = 'dusan.mlynarcik@email.cz';
        $billingBankAccount = '20-2245689978/2547';
        $comment = 'Přeji si zabalit celou objednávku do dárkového balení.';

        $cart = new ShoppingCart();
        $cart->setId($id);
        $cart->setIpAddress($ipAddress);
        $cart->setName($name);
        $cart->setEmail($email);
        $cart->setTelephone($telephone);
        $cart->setDeliveryAddress($deliveryAddress);
        $cart->setDeliveryCity($deliveryCity);
        $cart->setDeliveryPostalCode($deliveryPostalCode);
        $cart->setDeliveryCountry($deliveryCountry);
        $cart->setBillingName($billingName);
        $cart->setBillingAddress($billingAddress);
        $cart->setBillingCity($billingCity);
        $cart->setBillingPostalCode($billingPostalCode);
        $cart->setBillingCountry($billingCountry);
        $cart->setBillingIn($billingIn);
        $cart->setBillingVatId($billingVatId);
        $cart->setBillingTelephone($billingTelephone);
        $cart->setBillingEmail($billingEmail);
        $cart->setBillingBankAccount($billingBankAccount);
        $cart->setComment($comment);
        $cart->setBirthdayCoupon(TRUE);

        $hash = ShoppingCartHash::generateHash();
        $hashService = new ShoppingCartHash();
        $hashService->setHash($cart, $hash);

        Assert::same($ipAddress, $cart->getIpAddress());
        Assert::null($cart->getCustomerId());
        Assert::same($name, $cart->getName());
        Assert::same($email, $cart->getEmail());
        Assert::same($telephone, $cart->getTelephone());
        Assert::same($deliveryAddress, $cart->getDeliveryAddress());
        Assert::same($deliveryCity, $cart->getDeliveryCity());
        Assert::same($deliveryCountry, $cart->getDeliveryCountry());
        Assert::same($billingName, $cart->getBillingName());
        Assert::same($billingAddress, $cart->getBillingAddress());
        Assert::same($billingCity, $cart->getBillingCity());
        Assert::same($billingPostalCode, $cart->getBillingPostalCode());
        Assert::same($billingCountry, $cart->getBillingCountry());
        Assert::same($billingIn, $cart->getBillingIn());
        Assert::same($billingVatId, $cart->getBillingVatId());
        Assert::same($billingTelephone, $cart->getBillingTelephone());
        Assert::same($billingEmail, $cart->getBillingEmail());
        Assert::same($billingBankAccount, $cart->getBillingBankAccount());
        Assert::same($comment, $cart->getComment());
        Assert::true($cart->getBirthdayCoupon());
        Assert::same($hash, $cart->getHash());
    }
}

(new ShoppingCartHashTest())->run();