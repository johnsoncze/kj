<?php

declare(strict_types = 1);

namespace App\Tests\Order;

require_once __DIR__ . '/../bootstrap.php';

use App\Delivery\Delivery;
use App\Order\OrderFactory;
use App\Payment\Payment;
use App\ShoppingCart\Price\Price;
use App\ShoppingCart\ShoppingCartDTO;
use App\Tests\BaseTestCase;
use App\Tests\Delivery\DeliveryTestTrait;
use App\Tests\Delivery\Translation\DeliveryTranslationTestTrait;
use App\Tests\Payment\PaymentTestTrait;
use App\Tests\Payment\Translation\PaymentTranslationTestTrait;
use App\Tests\ShoppingCart\Delivery\ShoppingCartDeliveryTestTrait;
use App\Tests\ShoppingCart\Payment\ShoppingCartPaymentTestTrait;
use App\Tests\ShoppingCart\ShoppingCartTestTrait;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderFactoryTest extends BaseTestCase
{


    use DeliveryTestTrait;
    use DeliveryTranslationTestTrait;
    use PaymentTestTrait;
    use PaymentTranslationTestTrait;
    use ShoppingCartDeliveryTestTrait;
    use ShoppingCartPaymentTestTrait;
    use ShoppingCartTestTrait;

    /** @var OrderFactory|null */
    private $orderFactory;



    protected function setUp()
    {
        parent::setUp();
        $this->orderFactory = $this->container->getByType(OrderFactory::class);
    }



    /**
     * @dataProvider boolList
     * @param bool $requiredPaymentGateway
     */
    public function testCreateFromShoppingCart(bool $requiredPaymentGateway)
    {
        $delivery = $this->createTestDelivery();
        $delivery->setId(1);
        $delivery->setState(Delivery::ALLOWED);

        $deliveryTranslation = $this->createTestDeliveryTranslation();
        $delivery->addTranslation($deliveryTranslation);

        $payment = $this->createTestPayment();
        $payment->setId(1);
        $payment->setCreditCard($requiredPaymentGateway);
        $payment->setState(Payment::ALLOWED);

        $paymentTranslation = $this->createTestPaymentTranslation();
        $payment->addTranslation($paymentTranslation);

        $cart = $this->createTestShoppingCart();
        $cart->setBirthdayCoupon(FALSE);

        $cartDelivery = $this->createTestShoppingCartDelivery();
        $cartDelivery->setCatalogDelivery($delivery);

        $cartPayment = $this->createTestShoppingCartPayment();
        $cartPayment->setCatalogPayment($payment);

        $cartDTO = new ShoppingCartDTO($cart);
        $cartDTO->setDelivery($cartDelivery);
        $cartDTO->setPayment($cartPayment);
        $cartDTO->setPrice(new Price());

        $order = $this->orderFactory->createByShoppingCart(NULL, $cartDTO);

        Assert::same($requiredPaymentGateway, $order->isRequiredPaymentGateway());
        Assert::null($order->getPaymentGatewayTransactionId());
    }



    public function boolList() : array
    {
        return [
            [TRUE],
            [FALSE],
        ];
    }
}

(new OrderFactoryTest())->run();