<?php

declare(strict_types = 1);

namespace App\Tests\Order;

require_once __DIR__ . '/../bootstrap.php';

use App\Tests\BaseTestCase;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderTest extends BaseTestCase
{


    use OrderTestTrait;



    public function testCountProductSummaryPrice()
    {
        $order = $this->createTestOrder();

        Assert::same($order->getSummaryPrice() - $order->getDeliveryPrice() - $order->getPaymentPrice(), $order->countProductSummaryPrice());
    }



    public function testCountProductSummaryPriceWithoutVat()
    {
        $order = $this->createTestOrder();

        Assert::same($order->getSummaryPriceWithoutVat() - $order->getDeliveryPriceWithoutVat() - $order->getPaymentPriceWithoutVat(), $order->countProductSummaryPriceWithoutVat());
    }



    public function testGetProductSummaryPriceVat()
    {
        $order = $this->createTestOrder();
        $order->setProductSummaryPriceWithoutVat($order->countProductSummaryPriceWithoutVat());
        $order->setProductSummaryPrice($order->countProductSummaryPrice());

        Assert::same($order->getProductSummaryPrice() - $order->getProductSummaryPriceWithoutVat(), $order->getProductSummaryPriceVat());
    }
}

(new OrderTest())->run();