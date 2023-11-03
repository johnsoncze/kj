<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\NewsletterSubscriptionForm;

use App\Customer\Customer;
use App\Customer\CustomerStorageException;
use App\Customer\CustomerStorageFacadeFactory;
use App\FrontModule\Components\FormSpamProtection;
use App\Newsletter\Subscriber\SubscriberFacadeFactory;
use App\Newsletter\SubscriberFacadeException;
use Kdyby\Translation\ITranslator;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Form extends Control
{


    use FormSpamProtection;

    /** @var CustomerStorageFacadeFactory */
    private $customerStorageFacadeFactory;

    /** @var Customer|null */
    private $customer;

    /** @var Context */
    private $database;

    /** @var SubscriberFacadeFactory */
    private $subscriberFacadeFactory;

    /** @var ITranslator */
    private $translator;



    public function __construct(Context $context,
                                CustomerStorageFacadeFactory $customerStorageFacadeFactory,
                                ITranslator $translator,
                                SubscriberFacadeFactory $subscriberFacadeFactory)
    {
        parent::__construct();
        $this->customerStorageFacadeFactory = $customerStorageFacadeFactory;
        $this->database = $context;
        $this->subscriberFacadeFactory = $subscriberFacadeFactory;
        $this->translator = $translator;
    }



    /**
     * Setter for "customer" property
     * @param $customer Customer
     * @return self
     */
    public function setCustomer(Customer $customer) : self
    {
        $this->customer = $customer;
        return $this;
    }



    /**
     * @return \Nette\Application\UI\Form
     */
    public function createComponentForm() : \Nette\Application\UI\Form
    {
        $form = new \Nette\Application\UI\Form();
        $this->addSpamProtection($form);
        $form->addEmail('email')
            ->setAttribute('placeholder', $this->translator->translate('form.newsletter.subscription.input.email.placeholder'))
            ->setAttribute('class', 'TextField-input TextField-input--flat')
            ->setRequired($this->translator->translate('form.registration.error.email'))
            ->setMaxLength(50);
        $form->addSubmit('submit', $this->translator->translate('form.newsletter.subscription.input.submit.label'))
            ->setAttribute('class', 'Button');
        $form->onSuccess[] = [$this, 'formSuccess'];

        if ($this->customer !== NULL) {
            $form->setDefaults(['email' => $this->customer->getEmail()]);
        }

        return $form;
    }



    /**
     * @param $form \Nette\Application\UI\Form
     * @return void
     * @throws AbortException
     */
    public function formSuccess(\Nette\Application\UI\Form $form)
    {
        $values = $form->getValues();
        $presenter = $this->getPresenter();
        $this->processSpamRequest($values, $presenter, $this->translator);

        try {
            $this->database->beginTransaction();
            if ($this->customer !== NULL) {
                $customerFacade = $this->customerStorageFacadeFactory->create();
                $customerFacade->setNewsletter($this->customer->getId(), TRUE);
                $presenter->flashMessage($this->translator->translate('form.newsletter.subscription.message.successCustomer'), 'success');
            } else {
                $facade = $this->subscriberFacadeFactory->create();
                $facade->add($values->email);
                $presenter->flashMessage($this->translator->translate('form.newsletter.subscription.message.success'), 'success');
            }
            $this->database->commit();

            $presenter->redirect('this');
        } catch (CustomerStorageException $exception) {
            $this->database->rollBack();
        } catch (SubscriberFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage());
        }
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}