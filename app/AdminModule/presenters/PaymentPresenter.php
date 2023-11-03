<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\PaymentList\PaymentList;
use App\AdminModule\Components\PaymentList\PaymentListFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PaymentPresenter extends AdminModulePresenter
{


    /** @var PaymentListFactory @inject */
    public $paymentListFactory;



    /**
     * @return PaymentList
     */
    public function createComponentPaymentList() : PaymentList
    {
        return $this->paymentListFactory->create();
    }
}