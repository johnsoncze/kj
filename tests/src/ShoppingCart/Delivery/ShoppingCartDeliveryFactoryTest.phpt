<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Delivery;

use App\ShoppingCart\Delivery\ShoppingCartDeliveryFactory;
use App\Tests\BaseTestCase;
use App\Tests\Delivery\DeliveryTestTrait;
use App\Tests\Delivery\Translation\DeliveryTranslationTestTrait;
use App\Tests\ShoppingCart\ShoppingCartTestTrait;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartDeliveryFactoryTest extends BaseTestCase
{


    use DeliveryTestTrait;
    use DeliveryTranslationTestTrait;
    use ShoppingCartTestTrait;

    /** @var ShoppingCartDeliveryFactory|null */
    private $cartDeliveryFactory;



    protected function setUp()
    {
        parent::setUp();
        $this->cartDeliveryFactory = new ShoppingCartDeliveryFactory();
    }



    public function testCreate()
    {
        $cart = $this->createTestShoppingCart();
        $cart->setId(333);
        $delivery = $this->createTestDelivery();
        $delivery->setId(988);
        $deliveryTranslation = $this->createTestDeliveryTranslation();
        $delivery->addTranslation($deliveryTranslation);

        $cartDelivery = $this->cartDeliveryFactory->create($cart, $delivery);

        Assert::same($cart->getId(), $cartDelivery->getShoppingCartId());
        Assert::same($delivery->getId(), $cartDelivery->getDeliveryId());
        Assert::same($deliveryTranslation->getName(), $cartDelivery->getName());
        Assert::same(0.0, $cartDelivery->getDiscount());
        Assert::same($delivery->getPrice(), $cartDelivery->getPrice());
        Assert::same($delivery->getVat(), $cartDelivery->getVat());
    }
}

(new ShoppingCartDeliveryFactoryTest())->run();