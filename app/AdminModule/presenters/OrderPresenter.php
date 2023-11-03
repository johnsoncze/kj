<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\OrderList\OrderList;
use App\AdminModule\Components\OrderList\OrderListFactory;
use App\AdminModule\Components\OrderProductList\OrderProductList;
use App\AdminModule\Components\OrderProductList\OrderProductListFactory;
use App\AdminModule\Components\StateChangeForm\StateChangeForm;
use App\AdminModule\Components\StateChangeForm\StateChangeFormFactory;
use App\AdminModule\Components\PaymentChangeForm\PaymentChangeForm;
use App\AdminModule\Components\PaymentChangeForm\PaymentChangeFormFactory;
use App\Order\Order;
use App\Order\OrderRepository;
use App\Order\OrderStateFacadeException;
use App\Order\OrderStateFacadeFactory;
use App\Order\OrderPaymentFacadeException;
use App\Order\OrderPaymentFacadeFactory;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderPresenter extends AdminModulePresenter
{


    /** @var OrderListFactory @inject */
    public $orderListFactory;

    /** @var OrderProductListFactory @inject */
    public $orderProductListFactory;

    /** @var OrderStateFacadeFactory @inject */
    public $orderStateFacadeFactory;

    /** @var StateChangeFormFactory @inject */
    public $stateChangeFormFactory;

    /** @var OrderPaymentFacadeFactory @inject */
    public $orderPaymentFacadeFactory;

     /** @var PaymentChangeFormFactory @inject */
     public $paymentChangeFormFactory;

    /** @var Order|null */
    protected $order;



    /**
     * Action "detail"
     * @param $id int
     */
    public function actionDetail(int $id)
    {
        $this->order = $this->checkRequest($id, OrderRepository::class);

        $this->template->order = $this->order;
    }



    /**
     * Render "detail"
     */
    public function renderDetail()
    {
        $this->addToHeadline($this->order->getCode());
    }



    /**
     * @return OrderList
     */
    public function createComponentOrderList() : OrderList
    {
        return $this->orderListFactory->create();
    }



    /**
     * @return OrderProductList
     */
    public function createComponentOrderProductList() : OrderProductList
    {
        $list = $this->orderProductListFactory->create();
        $list->setOrder($this->order);
        return $list;
    }



    /**
     * @return StateChangeForm
     * @throws AbortException
     */
    public function createComponentStateForm() : StateChangeForm
    {
        $form = $this->stateChangeFormFactory->create();
        $form->setStateObject($this->order);
        $form->setSuccessCallback(function (Form $form) {
            try {
                $values = $form->getValues();

                $this->database->beginTransaction();
                $storageFacade = $this->orderStateFacadeFactory->create();
                $storageFacade->set($this->order->getId(), $values->state);
                $this->database->commit();

                $this->flashMessage('Stav byl uložen.', 'success');
                $this->redirect('this');
            } catch (OrderStateFacadeException $exception) {
                $this->database->rollBack();
                $this->flashMessage($exception->getMessage(), 'danger');
            }
        });
        return $form;
    }

    /**
     * @return PaymentChangeForm
     * @throws AbortException
     */
    public function createComponentPaymentChangeForm() : PaymentChangeForm
    {
        $form = $this->paymentChangeFormFactory->create();
      //  $form->setStateObject($this->order);
        $form->setSuccessCallback(function (Form $form) {
          $values = $form->getValues();
          if($values->payment){
            try {

                $this->database->beginTransaction();
                $storageFacade = $this->orderPaymentFacadeFactory->create();
                $storageFacade->set($this->order->getId(), $values->payment);
                $this->database->commit();

                $this->flashMessage('Typ platby byl uložen.', 'success');
                $this->redirect('this');
            } catch (OrderPaymentFacadeException $exception) {
                $this->database->rollBack();
                $this->flashMessage($exception->getMessage(), 'danger');
            }
          }
        });
        return $form;
    }
 }
