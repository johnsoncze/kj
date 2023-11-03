<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Payment;

use App\ShoppingCart\Payment\ShoppingCartPayment;
use App\ShoppingCart\Payment\ShoppingCartPaymentFactory;
use App\Tests\BaseTestCase;
use App\Tests\Payment\PaymentTestTrait;
use App\Tests\Payment\Translation\PaymentTranslationTestTrait;
use App\Tests\ShoppingCart\ShoppingCartTestTrait;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartPaymentFactoryTest extends BaseTestCase
{


    use PaymentTestTrait;
    use PaymentTranslationTestTrait;
    use ShoppingCartTestTrait;


    /** @var ShoppingCartPaymentFactory|null */
    protected $cartPaymentFactory;



    protected function setUp()
    {
        parent::setUp();
        $this->cartPaymentFactory = new ShoppingCartPaymentFactory();
    }



    public function testCreate()
    {
        $cart = $this->createTestShoppingCart();
        $cart->setId(5);
        $payment = $this->createTestPayment();
        $payment->setId(8);
        $paymentTranslation = $this->createTestPaymentTranslation();
        $payment->addTranslation($paymentTranslation);

        $cartPayment = $this->cartPaymentFactory->create($cart, $payment);

        Assert::type(ShoppingCartPayment::class, $cartPayment);
        Assert::same($cart->getId(), $cartPayment->getShoppingCartId());
        Assert::same($payment->getId(), $cartPayment->getPaymentId());
        Assert::same($paymentTranslation->getName(), $cartPayment->getName());
        Assert::same(0.0, $cartPayment->getDiscount());
        Assert::same($payment->getPrice(), $cartPayment->getPrice());
        Assert::same($payment->getVat(), $cartPayment->getVat());
    }
}

(new ShoppingCartPaymentFactoryTest())->run();