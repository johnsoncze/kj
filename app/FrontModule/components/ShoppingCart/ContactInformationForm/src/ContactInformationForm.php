<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\ShoppingCart\ContactInformationForm;

use App\Customer\Customer;
use App\FrontModule\Components\FormSpamProtection;
use App\FrontModule\Components\ShoppingCart\ContactInformationForm\Container\ContactInformationContainerFactory;
use App\Google\TagManager\DataLayer;
use App\Helpers\Regex;
use App\Helpers\Validators;
use App\Order\OrderCreateFacadeException;
use App\Order\OrderCreateFacadeFactory;
use App\ShoppingCart\ShoppingCart;
use App\ShoppingCart\ShoppingCartDTO;
use App\ShoppingCart\ShoppingCartSaveFacadeException;
use App\ShoppingCart\ShoppingCartSaveFacadeFactory;
use Kdyby\Translation\ITranslator;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Nette\InvalidStateException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ContactInformationForm extends Control
{


    use FormSpamProtection;

    /** @var string form container types */
    const CONTAINER_CONTACT = 'contact';
    const CONTAINER_DELIVERY = 'delivery';


    /** @var ContactInformationContainerFactory */
    private $contactInformationContainerFactory;

    /** @var Customer|null */
    private $customer;

    /** @var Context */
    private $database;

    /** @var DataLayer */
    private $dataLayer;

    /** @var OrderCreateFacadeFactory */
    private $orderCreateFacadeFactory;

    /** @var ShoppingCartDTO|null */
    private $shoppingCart;

    /** @var ShoppingCartSaveFacadeFactory */
    private $shoppingCartSaveFacadeFactory;

    /** @var ITranslator */
    private $translator;



    public function __construct(ContactInformationContainerFactory $contactInformationContainerFactory,
                                Context $context,
                                DataLayer $dataLayer,
                                ITranslator $translator,
                                OrderCreateFacadeFactory $orderCreateFacadeFactory,
                                ShoppingCartSaveFacadeFactory $shoppingCartSaveFacadeFactory)
    {
        parent::__construct();
        $this->contactInformationContainerFactory = $contactInformationContainerFactory;
        $this->database = $context;
        $this->dataLayer = $dataLayer;
        $this->orderCreateFacadeFactory = $orderCreateFacadeFactory;
        $this->shoppingCartSaveFacadeFactory = $shoppingCartSaveFacadeFactory;
        $this->translator = $translator;
    }



    /**
     * @param $customer Customer
     * @return self
     */
    public function setCustomer(Customer $customer) : self
    {
        $this->customer = $customer;
        return $this;
    }



    /**
     * @param $shoppingCart ShoppingCartDTO
     * @return self
     */
    public function setShoppingCart(ShoppingCartDTO $shoppingCart) : self
    {
        $this->shoppingCart = $shoppingCart;
        return $this;
    }



    /**
     * @return Form
     * @throws InvalidStateException
     */
    public function createComponentForm() : Form
    {
        $form = new Form();
        $this->addSpamProtection($form);
        $form->addComponent($this->contactInformationContainerFactory->create()->setConfigure(), self::CONTAINER_CONTACT);

        //delivery address
        $deliveryAddress = $form->addCheckbox('deliveryAddress', $this->translator->translate('form.shoppingCart.contactInformation.input.deliveryAddress.label'));
        $deliveryContainer = $this->contactInformationContainerFactory->create();
        $deliveryContainer->setConfigure($deliveryAddress);
        $deliveryContainer->removeComponent($deliveryContainer->getComponent('email'));
        $deliveryContainer->addText('company', $this->translator->translate('form.shoppingCart.contactInformation.input.company.label'))
            ->setMaxLength(50)->setRequired(false);

        $form->addComponent($deliveryContainer, self::CONTAINER_DELIVERY);

        $form->addTextArea('comment', $this->translator->translate('form.opportunity.input.comment.label'))
            ->setMaxLength(ShoppingCart::MAX_LENGTH_COMMENT);
//        $form->addCheckbox('terms', $this->translator->translate('form.shoppingCart.contactInformation.input.terms.label', ['link' => $this->presenter->link('Page:default', ['url' => 'obchodni-podminky'])]))
//            ->setRequired($this->translator->translate('form.shoppingCart.contactInformation.input.terms.require'));
        $form->addSubmit('submit')
            ->setHtmlId('contactInformationSubmit');

        $form->onSuccess[] = [$this, 'formSuccess'];
        $presenter = $this->getPresenter();
        $r = $presenter->getHttpRequest()->getReferer();
        //TODO: remove  this quick hack
        if ($r && $r->getPath() == '/kosik/rekapitulace') {
            $this->setDefaultFormValues($form);
        }elseif ($presenter->getUser()->isLoggedIn()){
            $this->setDefaultFormValues($form);
        }

        return $form;
    }


    /**
     * Handler for success sent form.
     * @param $form Form
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $presenter = $this->getPresenter();
        $this->processSpamRequest($form->getValues(), $presenter, $this->translator);

        $values = $form->getValues();
        if (!Validators::isEmail($values['contact']['email'])) {
            $form->addError($this->translator->translate('general.error.invalidEmailFormat'));
        }
        if (!preg_match('/' . Regex::POSTCODE . '/', $values['contact']['postCode'])) {
            $form->addError($this->translator->translate('form.contact.error.postCode'));
        }

        if ($values['deliveryAddress'] && !preg_match('/' . Regex::POSTCODE . '/', $values['delivery']['postCode'])) {
            $form->addError($this->translator->translate('form.contact.error.postCode'));
        }

        if (!$form->hasErrors()) {
            try {
                //save data
                $this->database->beginTransaction();
                $this->saveContactInformation($form);
                $this->database->commit();
                $presenter->redirect('ShoppingCart:step3Recapitulation');
            } catch (ShoppingCartSaveFacadeException $exception) {
                $this->database->rollBack();
                $presenter->flashMessage($exception->getMessage(), 'danger');
            } catch (OrderCreateFacadeException $exception) {
                $this->database->rollBack();
                $presenter->flashMessage($exception->getMessage(), 'danger');
            }
        }
    }



    public function render()
    {
        $this->template->customer = $this->customer;
        $this->template->shoppingCart = $this->shoppingCart;

        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * todo maybe move to separed component?
     * @throws AbortException
     */
    public function handleApplyBirthdayDiscount()
    {
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $cartSave = $this->shoppingCartSaveFacadeFactory->create();
            $cartSave->applyBirthdayCoupon((int)$this->shoppingCart->getEntity()->getId());
            $this->database->commit();

            $presenter->flashMessage($this->translator->translate('shopping-cart.birthdaycoupon.applied'), 'success');
            $presenter->redirect('this');
        } catch (ShoppingCartSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    /**
     * todo maybe move to separed component?
     * @throws AbortException
     */
    public function handleRemoveBirthdayDiscount()
    {
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $cartSave = $this->shoppingCartSaveFacadeFactory->create();
            $cartSave->removeBirthdayCoupon((int)$this->shoppingCart->getEntity()->getId());
            $this->database->commit();

            $presenter->flashMessage($this->translator->translate('shopping-cart.birthdaycoupon.removed'), 'success');
            $presenter->redirect('this');
        } catch (ShoppingCartSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    /**
     * @param $form Form
     * @return Form
     */
    private function setDefaultFormValues(Form $form) : Form
    {
        $shoppingCart = $this->shoppingCart->getEntity();
        $form->setDefaults([
            'deliveryAddress' => $shoppingCart->getDeliveryAddress() !== NULL,
            'comment' => $shoppingCart->getComment(),
            self::CONTAINER_CONTACT => [
                'firstName' => $shoppingCart->getFirstName() ?: ($this->customer ? $this->customer->getFirstName() : NULL),
                'lastName' => $shoppingCart->getLastName() ?: ($this->customer ? $this->customer->getLastName() : NULL),
                'email' => $shoppingCart->getBillingEmail() ?: ($this->customer ? $this->customer->getEmail() : NULL),
                'telephone' => $shoppingCart->getBillingTelephone() ?: ($this->customer ? $this->customer->getTelephone() : NULL),
                'street' => $shoppingCart->getBillingAddress() ?: ($this->customer ? $this->customer->getStreet() : NULL),
                'city' => $shoppingCart->getBillingCity() ?: ($this->customer ? $this->customer->getCity() : NULL),
                'postCode' => $shoppingCart->getBillingPostalCode() ?: ($this->customer ? $this->customer->getPostcode() : NULL),
                'state' => $shoppingCart->getBillingCountry() ?: ($this->customer ? $this->customer->getCountryCode() : NULL),
            ],
            self::CONTAINER_DELIVERY => [
                'firstName' => $shoppingCart->getDeliveryFirstName(),
                'lastName' => $shoppingCart->getDeliveryLastName(),
                'company' => $shoppingCart->getDeliveryCompany(),
                'telephone' => $shoppingCart->getTelephone(),
                'street' => $shoppingCart->getDeliveryAddress(),
                'city' => $shoppingCart->getDeliveryCity(),
                'postCode' => $shoppingCart->getDeliveryPostalCode(),
                'state' => $shoppingCart->getDeliveryCountry(),
            ],
        ]);
        return $form;
    }



    /**
     * Save contact information from form.
     * @param $form Form
     * @return Form
     * @throws ShoppingCartSaveFacadeException
     */
    private function saveContactInformation(Form $form) : Form
    {
        $values = $form->getValues();

        if (isset($values->{self::CONTAINER_DELIVERY}->postCode) && $values->{self::CONTAINER_DELIVERY}->postCode) {
            $values->{self::CONTAINER_DELIVERY}->postCode = str_replace(' ', '', $values->{self::CONTAINER_DELIVERY}->postCode);
        }
        if (isset($values->{self::CONTAINER_CONTACT}->postCode) && $values->{self::CONTAINER_CONTACT}->postCode) {
            $values->{self::CONTAINER_CONTACT}->postCode = str_replace(' ', '', $values->{self::CONTAINER_CONTACT}->postCode);
        }
        $cartFacade = $this->shoppingCartSaveFacadeFactory->create();
        $cartFacade->update($this->shoppingCart->getEntity()->getId(),
            NULL,
            $values->{self::CONTAINER_CONTACT}->firstName,
            $values->{self::CONTAINER_CONTACT}->lastName,
            $values->{self::CONTAINER_CONTACT}->email,
            ($values->{self::CONTAINER_DELIVERY}->telephone ?: $values->{self::CONTAINER_CONTACT}->telephone),
            $values->deliveryAddress ? $values->{self::CONTAINER_DELIVERY}->firstName : NULL,
            $values->deliveryAddress ? $values->{self::CONTAINER_DELIVERY}->lastName : NULL,
            $values->deliveryAddress ? $values->{self::CONTAINER_DELIVERY}->company : NULL,
            $values->deliveryAddress ? $values->{self::CONTAINER_DELIVERY}->street : NULL,
            $values->deliveryAddress ? $values->{self::CONTAINER_DELIVERY}->city : NULL,
            $values->deliveryAddress ? $values->{self::CONTAINER_DELIVERY}->postCode : NULL,
            $values->deliveryAddress ? $values->{self::CONTAINER_DELIVERY}->state : NULL,
            NULL, NULL,
            $values->{self::CONTAINER_CONTACT}->street,
            $values->{self::CONTAINER_CONTACT}->city,
            $values->{self::CONTAINER_CONTACT}->postCode,
            $values->{self::CONTAINER_CONTACT}->state,
            NULL,
            NULL,
            $values->{self::CONTAINER_CONTACT}->telephone,
            $values->{self::CONTAINER_CONTACT}->email,
            NULL,
            $values->comment ?: NULL);

        return $form;
    }
}