<?php

declare(strict_types = 1);

namespace App\Periskop\Order;

use App\Order\Order;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderHelpers
{


    /** @var int */
    const ORDER_ID_PREFIX = 999;



    private function __construct()
    {
    }



    /**
     * Create order id for Periskop,
     * because Periskop does not distinguish orders
     * from different online stores, and this method
     * is a workaround.
     *
     * @param $order Order
     * @return int
     */
    public static function createOrderId(Order $order) : int
    {
        $id = self::ORDER_ID_PREFIX . $order->getId();
        return (int)$id;
    }



    /**
     * Reverse function to ::createOrderId() method.
     *
     * @param $id int
     * @return int
     */
    public static function getOrderId(int $id) : int
    {
        $idString = (string)$id;
        if (strpos($idString, (string)self::ORDER_ID_PREFIX) === 0) {
            return (int)substr($idString, 3);
        }
        return $id;
    }
}