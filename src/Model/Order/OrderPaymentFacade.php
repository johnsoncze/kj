<?php

declare(strict_types = 1);

namespace App\Order;

use App\Order\Email\SendEmail;
use App\Payment\PaymentAllowedRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderPaymentFacade
{


    /** @var OrderRepository */
    private $orderRepo;

    /** @var SendEmail */
    private $sendEmail;

    /** @var PaymentAllowedRepository */
    private $paymentRepo;


    public function __construct(OrderRepository $orderRepository,
                                SendEmail $sendEmail, PaymentAllowedRepository $paymentRepo)
    {
        $this->orderRepo = $orderRepository;
        $this->sendEmail = $sendEmail;
        $this->paymentRepo = $paymentRepo;
    }



    /**
     * Set a payment on order.
     * @param $orderId int
     * @param $payment string
     * @return Order
     * @throws OrderPaymentFacadeException
     * todo test
     */
    public function set(int $orderId, int $payment) : Order
    {
        try {
            $order = $this->orderRepo->getOneById($orderId);
            return $this->savePayment($order, $payment);
        } catch (OrderNotFoundException $exception) {
            throw new OrderPaymentFacadeException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new OrderPaymentFacadeException($exception->getMessage());
        }
    }


    /**
     * @param $order Order
     * @param $payment string
     * @return Order
     * @throws OrderPaymentFacadeException
     */
     private function savePayment(Order $order, int $newPayment) : Order
     {
         $currentPayment = $order->getPaymentId();
         if ( $currentPayment === $newPayment) {
             throw new OrderPaymentFacadeException('Typ platby je již nastaven.');
         }

         try {
             $order->setPaymentId($newPayment);
             $paymentList = array();
             $list = $this->paymentRepo->findAll();
             foreach ($list as $k => $l) {
               $paymentList[$k]["name"] = $l->getTranslation()->getName();
               $paymentList[$k]["ext"] = $l->getExternalSystemId();
             }
             $order->setPaymentExternalSystemId($paymentList[$newPayment]["ext"]);
             $order->setPaymentName($paymentList[$newPayment]["name"]);
             if($currentPayment == 1) //zmena z platby kartou
             {
               $order->setIsRequiredPaymentGateway(false);
               //$order->setPaymentGatewayTransactionId("");
               $order->setPaymentGatewayTransactionState("cancelled");
             }

             $this->orderRepo->save($order);
         } catch (\EntityInvalidArgumentException $exception) {
             throw new OrderPaymentFacadeException($exception->getMessage());
         }

         //send email
         // if (method_exists($this->sendEmail, $payment)) {
         //     $this->sendEmail->{$payment}($order); //send email is set on payment
         // }

         return $order;
     }
}
