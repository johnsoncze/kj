<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\ShoppingCart\DeliveryForm;

use App\Delivery\Delivery;
use App\Delivery\DeliveryAllowedRepository;
use App\Google\TagManager\DataLayer;
use App\Google\TagManager\EnhancedEcommerce\DataFactory;
use App\Helpers\Entities;
use App\Payment\Payment;
use App\Payment\PaymentAllowedRepository;
use App\ShoppingCart\Delivery\ShoppingCartDeliverySaveFacadeException;
use App\ShoppingCart\Delivery\ShoppingCartDeliverySaveFacadeFactory;
use App\ShoppingCart\Payment\ShoppingCartPaymentSaveFacadeException;
use App\ShoppingCart\Payment\ShoppingCartPaymentSaveFacadeFactory;
use App\ShoppingCart\ShoppingCartDTO;
use Kdyby\Translation\ITranslator;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class DeliveryForm extends Control
{


    /** @var ShoppingCartDeliverySaveFacadeFactory */
    private $cartDeliverySaveFacadeFactory;

    /** @var ShoppingCartPaymentSaveFacadeFactory */
    private $cartPaymentSaveFacadeFactory;

    /** @var Context */
    private $database;

    /** @var DataLayer */
    private $dataLayer;

    /** @var Delivery[]|array */
    private $delivery;

    /** @var DeliveryAllowedRepository */
    private $deliveryRepo;

    /** @var Payment[]|array */
    private $payment;

    /** @var PaymentAllowedRepository */
    private $paymentRepo;

    /** @var ShoppingCartDTO|null */
    private $shoppingCart;

    /** @var ITranslator */
    private $translator;



    public function __construct(Context $database,
								DataLayer $dataLayer,
                                DeliveryAllowedRepository $deliveryRepo,
                                PaymentAllowedRepository $paymentRepo,
                                ITranslator $translator,
                                ShoppingCartDeliverySaveFacadeFactory $shoppingCartDeliverySaveFacadeFactory,
                                ShoppingCartPaymentSaveFacadeFactory $shoppingCartPaymentSaveFacadeFactory)
    {
        parent::__construct();
        $this->cartDeliverySaveFacadeFactory = $shoppingCartDeliverySaveFacadeFactory;
        $this->cartPaymentSaveFacadeFactory = $shoppingCartPaymentSaveFacadeFactory;
        $this->database = $database;
        $this->dataLayer = $dataLayer;
        $this->deliveryRepo = $deliveryRepo;
        $this->paymentRepo = $paymentRepo;
        $this->translator = $translator;
    }



    /**
     * @param $shoppingCart ShoppingCartDTO
     * @return self
     */
    public function setShoppingCart(ShoppingCartDTO $shoppingCart) : self
    {
        $this->shoppingCart = $shoppingCart;
        $this->delivery = $this->deliveryRepo->findAll();
        $this->payment = $this->paymentRepo->findAll();

        return $this;
    }



    /**
     * @return ShoppingCartDTO
     * @throws \InvalidArgumentException missing shopping cart object
     */
    public function getShoppingCart() : ShoppingCartDTO
    {
        if ($this->shoppingCart === NULL) {
            throw new \InvalidArgumentException('Missing shopping cart.');
        }
        return $this->shoppingCart;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $deliveryList = $this->delivery ? Entities::toPair($this->delivery, 'id', 'id') : [];
        $paymentList = $this->payment ? Entities::toPair($this->payment, 'id', 'id') : [];

        $form = new Form();
        $form->addRadioList('delivery', NULL, $deliveryList)
            ->setRequired($this->translator->translate('form.delivery.input.delivery.require'));
        $form->addRadioList('payment', NULL, $paymentList)
            ->setRequired($this->translator->translate('form.delivery.input.payment.require'));
        $form->addSubmit('submit')
            ->setHtmlId('deliveryFormSubmit');
        $form->onSuccess[] = [$this, 'formSuccess'];
        $this->setDefaultFormValues($form, $deliveryList, $paymentList);

        return $form;
    }



    /**
     * @param $form Form
     * @return void
     * @throws AbortException
     * @throws \InvalidArgumentException
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $shoppingCart = $this->getShoppingCart();
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $cartDelivery = $this->cartDeliverySaveFacadeFactory->create()->save($shoppingCart->getEntity()->getId(), $values->delivery);
            $paymentDelivery = $this->cartPaymentSaveFacadeFactory->create()->save($shoppingCart->getEntity()->getId(), $values->payment);
            $this->database->commit();

            $additionalData['option'] = sprintf('%s - %s', $cartDelivery->getTranslatedName(), $paymentDelivery->getTranslatedName());
            $data = DataFactory::create($this->shoppingCart, 3, $additionalData);
            $this->dataLayer->add($data);

            $presenter->redirect('ShoppingCart:step1links');
        } catch (ShoppingCartDeliverySaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        } catch (ShoppingCartPaymentSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    public function render()
    {
        $this->template->delivery = $this->delivery;
        $this->template->payment = $this->payment;

        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @param $form Form
     * @param $deliveryList array
     * @param $paymentList array
     * @return Form
     */
    public function setDefaultFormValues(Form $form, array $deliveryList = [], array $paymentList = []) : Form
    {
        $defaultDelivery = 4;
        $cartDelivery = $this->shoppingCart->getDelivery();
        $cartPayment = $this->shoppingCart->getPayment();
        $form->setDefaults([
            'delivery' => $cartDelivery && array_key_exists($cartDelivery->getDeliveryId(), $deliveryList) ? $cartDelivery->getDeliveryId() : (array_key_exists($defaultDelivery, $deliveryList) ? $defaultDelivery : null),
            'payment' => $cartPayment && array_key_exists($cartPayment->getPaymentId(), $paymentList) ? $cartPayment->getPaymentId() : NULL,
        ]);
        return $form;
    }
}