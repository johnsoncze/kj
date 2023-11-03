<?php

declare(strict_types = 1);

namespace App\Order;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OrderCreateFacadeException extends \Exception
{


}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OrderFacadeException extends \Exception
{


    /** @var int */
    const ORDER_PAID = 100;
    const PAYMENT_GATEWAY_REQUEST_ERROR = 101;
}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OrderNotFoundException extends \Exception
{


}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OrderStateFacadeException extends \Exception
{


}
/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OrderPaymentFacadeException extends \Exception
{


}
