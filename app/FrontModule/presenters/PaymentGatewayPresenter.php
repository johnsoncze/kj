<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Order\Order;
use App\Order\OrderFacadeException;
use App\Order\OrderFacadeFactory;
use App\Order\OrderNotFoundException;
use App\Order\OrderRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class PaymentGatewayPresenter extends AbstractPresenter
{


    /** @var OrderFacadeFactory @inject */
    public $orderFacadeFactory;

    /** @var OrderRepository @inject */
    public $orderRepo;



    /**
     * @param $token string
     * @return void
     * @throws AbortException
     * @throws BadRequestException
     */
    public function actionCreateRequest(string $token)
    {
        try {
            $this->database->beginTransaction();
            $orderFacade = $this->orderFacadeFactory->create();
            $url = $orderFacade->createPaymentGatewayRequest($token);
            $this->database->commit();
            $this->redirectUrl($url, 302);
        } catch (OrderFacadeException $exception) {
            $this->database->rollBack();
            $code = $exception->getCode();
            $this->logger->addError(sprintf('payment.gateway.create.request: An error has been occurred. Error: %s', $exception->getMessage()), ['orderToken' => $token]);
            if ($code === OrderFacadeException::PAYMENT_GATEWAY_REQUEST_ERROR) {
            	$this->template->title = $this->translator->translate('payment.gateway.request.error.title');
                $this->template->setFile(__DIR__ . '/templates/PaymentGateway/requestError.latte');
            } elseif ($code === OrderFacadeException::ORDER_PAID) {
				$this->template->title = $this->translator->translate('payment.gateway.paidAlready.title');
                $this->template->setFile(__DIR__ . '/templates/PaymentGateway/paidAlready.latte');
            } else {
                throw new BadRequestException(NULL, 404);
            }
        }

        $this->template->index = FALSE;
    }



    /**
     * @param $id string
     * @param $token string
     * @return void
     * @throws BadRequestException
     */
    public function actionPaid(string $id, string $token)
    {
        try {
            $order = $this->orderRepo->getOneByTokenAndPaymentGatewayTransactionId($token, $id);
            if ($order->isPaidByPaymentGateway() !== TRUE) {
                throw new BadRequestException(NULL, 404);
            }
            $this->template->index = FALSE;
            $this->template->order = $order;
            $this->template->title = $this->translator->translate('payment.gateway.paidTitle');
        } catch (OrderNotFoundException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
     * @param $id string
     * @param $token string
     * @return void
     * @throws BadRequestException
     */
    public function actionPending(string $id, string $token)
    {
        try {
            $order = $this->orderRepo->getOneByTokenAndPaymentGatewayTransactionId($token, $id);
			if ($order->getPaymentGatewayTransactionState() !== Order::PENDING_PAYMENT_GATEWAY_STATE) {
				throw new BadRequestException(NULL, 404);
			}
            $this->template->index = FALSE;
            $this->template->order = $order;
			$this->template->title = $this->translator->translate('payment.gateway.pending.title');
        } catch (OrderNotFoundException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
     * @param $id string
     * @param $token string
     * @return void
     * @throws BadRequestException
     */
    public function actionCancelled(string $id, string $token)
    {
        try {
            $order = $this->orderRepo->getOneByTokenAndPaymentGatewayTransactionId($token, $id);
            if ($order->getPaymentGatewayTransactionState() !== Order::CANCELLED_PAYMENT_GATEWAY_STATE) {
                throw new BadRequestException(NULL, 404);
            }
            $this->template->index = FALSE;
            $this->template->order = $order;
			$this->template->title = $this->translator->translate('payment.gateway.cancelled.title');
        } catch (OrderNotFoundException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
     * @return void
     * @throws BadRequestException
     * @throws AbortException
     */
    public function actionReceiveState()
    {
        $code = 0;
        $message = 'OK';
        $request = $this->getRequest();
        $data = $request->getPost();
        $transId = $data['transId'] ?? NULL;
        $token = $data['refId'] ?? NULL;
        $state = $data['status'] ?? NULL;
        $logParams = ['data' => $data];

        $this->logger->addDebug('payment.gateway.receive.state: Received request.', $logParams);

        if ($transId === NULL || $token === NULL || $state === NULL) {
            $code = 1;
            $message = 'Missing parameters';
            $this->logger->addError('payment.gateway.receive.state: Missing some required parameter.', ['transId' => $transId, 'token' => $token, 'state' => $state]);
        } else {
            try {
                $this->database->beginTransaction();
                $orderFacade = $this->orderFacadeFactory->create();
                $orderFacade->setPaymentGatewayTransactionState($transId, $token, $state);
                $this->database->commit();
                $this->logger->addInfo('payment.gateway.receive.state: State has been set.', $logParams);
            } catch (OrderFacadeException $exception) {
                $this->database->rollBack();
                $this->logger->addError(sprintf('payment.gateway.receive.state: An error has been occurred. Error: %s', $exception->getMessage()), $logParams);
                $code = 1;
                $message = 'An error has been occurred.';
            }
        }

        $this->template->code = $code;
        $this->template->message = $message;
    }
}