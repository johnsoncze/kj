<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\PaymentChangeForm;

use App\Payment\Payment;
use App\Payment\PaymentAllowedRepository;
use App\Helpers\Arrays;
use App\Payment\Translation\PaymentTranslation;
use Kdyby\Translation\ITranslator;
use App\Helpers\Entities;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class PaymentChangeForm extends Control
{
  /** @var ITranslator */
  private $translator;

  /** @var PaymentAllowedRepository */
  private $paymentRepo;

  /** @var callable|null */
  private $successCallback;


    public function __construct(PaymentAllowedRepository $paymentRepo, ITranslator $translator)
    {
        parent::__construct();
        $this->paymentRepo = $paymentRepo;
        $this->translator = $translator;
    }


    /**
     * @param callable $callback
     * @return self
     */
    public function setSuccessCallback(callable $callback) : self
    {
        $this->successCallback = $callback;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $paymentList[0] = "Změnit typ platby";
        $list = $this->paymentRepo->findAll();
        foreach ($list as $k => $l) {
          if ($k ==1) continue; //odstraneni platby kartou
          $paymentList[$k] = $l->getTranslation()->getName();
        }

        $form = new Form();
        $form->addSelect('payment', 'Platba: ', $paymentList)
            ->setAttribute('class', 'form-control')
            ;
        $form->addSubmit('submit', 'Uložit')
            ->setAttribute('class', 'btn btn-success');
        $form->onSuccess[] = $this->successCallback;
        return $form;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
