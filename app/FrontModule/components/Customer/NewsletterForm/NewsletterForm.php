<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Customer\NewsletterForm;

use App\Customer\Customer;
use App\Customer\CustomerStorageException;
use App\Customer\CustomerStorageFacadeFactory;
use Kdyby\Translation\ITranslator;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class NewsletterForm extends Control
{


    /** @var Customer|null */
    private $customer;

    /** @var CustomerStorageFacadeFactory */
    private $customerStorageFacadeFactory;

    /** @var Context */
    private $database;

    /** @var ITranslator */
    private $translator;



    public function __construct(Context $context,
                                CustomerStorageFacadeFactory $customerStorageFacadeFactory,
                                ITranslator $translator)
    {
        parent::__construct();
        $this->database = $context;
        $this->customerStorageFacadeFactory = $customerStorageFacadeFactory;
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
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $form = new Form();
        $form->addCheckbox('newsletter', $this->translator->translate('form.newsletter.customer.input.newsletter.label'))
            ->setDefaultValue($this->customer->wantNewsletter());
        $form->addSubmit('submit', $this->translator->translate('form.newsletter.customer.input.submit.label'));
        $form->onSuccess[] = [$this, 'formSuccess'];
        return $form;
    }



    /**
     * @param $form Form
     * @return void
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $customerFacade = $this->customerStorageFacadeFactory->create();
            $customerFacade->setNewsletter($this->customer->getId(), $form->getValues()->newsletter === TRUE);
            $this->database->commit();

            $presenter->flashMessage($this->translator->translate('form.newsletter.customer.message.success'), 'success');
            $presenter->redirect('this');
        } catch (CustomerStorageException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}