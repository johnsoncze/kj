<?php

declare(strict_types = 1);

namespace App\Order;

use App\Order\Email\SendEmail;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderStateFacade
{


    /** @var OrderRepository */
    private $orderRepo;

    /** @var SendEmail */
    private $sendEmail;



    public function __construct(OrderRepository $orderRepository,
                                SendEmail $sendEmail)
    {
        $this->orderRepo = $orderRepository;
        $this->sendEmail = $sendEmail;
    }



    /**
     * Set a state on order.
     * @param $orderId int
     * @param $state string
     * @return Order
     * @throws OrderStateFacadeException
     * todo test
     */
    public function set(int $orderId, string $state) : Order
    {
        try {
            $order = $this->orderRepo->getOneById($orderId);
            return $this->saveState($order, $state);
        } catch (OrderNotFoundException $exception) {
            throw new OrderStateFacadeException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new OrderStateFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $orderId int
     * @param $stateId int
     * @return Order
     * @throws OrderStateFacadeException
     * todo test
     */
    public function setByExternalSystemStateId(int $orderId, int $stateId) : Order
    {
        try {
            $order = $this->orderRepo->getOneById($orderId);
            $state = Order::getStateByExternalSystemId($stateId)['key'];
            if ($order->wasSentToExternalSystem() !== TRUE) {
                $order->setSentToExternalSystem(TRUE);
            }
            return $this->saveState($order, $state);
        } catch (OrderNotFoundException $exception) {
            throw new OrderStateFacadeException($exception->getMessage());
        } catch (\InvalidArgumentException $exception) {
            throw new OrderStateFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $order Order
     * @param $state string
     * @return Order
     * @throws OrderStateFacadeException
     */
    private function saveState(Order $order, string $state) : Order
    {
        if ($order->getState() === $state) {
            throw new OrderStateFacadeException('Stav je již nastaven.');
        }

        try {
            $order->setState($state);
            $this->orderRepo->save($order);
        } catch (\EntityInvalidArgumentException $exception) {
            throw new OrderStateFacadeException($exception->getMessage());
        }

        //send email
        if (method_exists($this->sendEmail, $state)) {
            $this->sendEmail->{$state}($order); //send email is set on state
        }

        return $order;
    }
}