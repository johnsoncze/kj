<?php

declare(strict_types = 1);

namespace App\Order;

use App\ComGate\Request\ParameterFactory;
use App\ComGate\Request\RequestSender;
use App\GuzzleHttp\Guzzle\ClientException;
use App\Order\Email\SendEmail;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderFacade
{


    /** @var ParameterFactory */
    private $comgateParameterFactory;

    /** @var SendEmail */
    private $emailSender;

    /** @var OrderRepository */
    private $orderRepo;

    /** @var RequestSender */
    private $requestSender;



    public function __construct(OrderRepository $orderRepo,
                                ParameterFactory $parameterFactory,
                                RequestSender $requestSender,
                                SendEmail $sendEmail)
    {
        $this->comgateParameterFactory = $parameterFactory;
        $this->emailSender = $sendEmail;
        $this->orderRepo = $orderRepo;
        $this->requestSender = $requestSender;
    }



    /**
     * @param $id int
     * @param $sentToEETracking bool
     * @return Order
     * @throws OrderFacadeException
     */
    public function update(int $id, bool $sentToEETracking = FALSE) : Order
    {
        try {
            $order = $this->orderRepo->getOneById($id);
            $order->setSentToEETracking($sentToEETracking);
            $this->orderRepo->save($order);
            return $order;
        } catch (OrderNotFoundException $exception) {
            throw new OrderFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $token string
     * @return string url for redirect to payment
     * @throws OrderFacadeException
     * todo test
     */
    public function createPaymentGatewayRequest(string $token) : string
    {
        try {
            $order = $this->orderRepo->getOneByToken($token);
            if ($order->isRequiredPaymentGateway() !== TRUE) {
                throw new OrderFacadeException('Payment is not required for payment gateway.');
            }
            if ($order->isPaidByPaymentGateway() === TRUE) {
                throw new OrderFacadeException('Order has been paid.', OrderFacadeException::ORDER_PAID);
            }

            try {
                $parameters = $this->comgateParameterFactory->createCreateParametersFromOrder($order);
                $response = $this->requestSender->createPayment((array)$parameters);

                //save values to order
                $order->setPaymentGatewayTransactionId($response->getId());
                $order->setPaymentGatewayTransactionState(Order::NEW_PAYMENT_GATEWAY_STATE);
                $this->orderRepo->save($order);
            } catch (\InvalidArgumentException $exception) {
                throw new OrderFacadeException($exception->getMessage(), OrderFacadeException::PAYMENT_GATEWAY_REQUEST_ERROR);
            } catch (ClientException $exception) {
                throw new OrderFacadeException($exception->getMessage(), OrderFacadeException::PAYMENT_GATEWAY_REQUEST_ERROR);
            }

            return $response->getRedirect();
        } catch (OrderNotFoundException $exception) {
            throw new OrderFacadeException($exception->getMessage());
        } catch (ClientException $exception) {
            throw new OrderFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $transactionId string
     * @param $token string
     * @param $state string
     * @return Order
     * @throws OrderFacadeException
     * todo test
     */
    public function setPaymentGatewayTransactionState(string $transactionId, string $token, string $state) : Order
    {
        try {
            $order = $this->orderRepo->getOneByTokenAndPaymentGatewayTransactionId($token, $transactionId);
            if ($order->isPaidByPaymentGateway() === TRUE) {
                throw new OrderFacadeException('Order has been paid. Why is changing state?');
            }
            if ($order->getPaymentGatewayTransactionState() === $state) {
                throw new OrderFacadeException(sprintf('State \'%s\' is set already.', $state));
            }
            $order->setPaymentGatewayTransactionState($state);
            $state === Order::PAID_PAYMENT_GATEWAY_STATE && $order->getState() === Order::NEW_STATE ? $order->setState(Order::ACCEPTED_STATE) : NULL;
            $this->orderRepo->save($order);
            $emailMethod = $order->getPaymentGatewayEmailMethodName();
            if (method_exists($this->emailSender, $emailMethod)) {
                $this->emailSender->{$emailMethod}($order);
            }
            return $order;
        } catch (OrderNotFoundException $exception) {
            throw new OrderFacadeException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new OrderFacadeException($exception->getMessage());
        }
    }
}