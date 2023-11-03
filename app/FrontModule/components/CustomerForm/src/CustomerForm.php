<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\CustomerForm;

use App\Customer\Customer;
use App\Customer\CustomerStorageException;
use App\Customer\CustomerStorageFacadeFactory;
use App\FrontModule\Components\BirthdayFormContainer\BirthdayFormContainer;
use App\FrontModule\Components\Registration\Form\AbstractRegistrationForm;
use Kdyby\Translation\ITranslator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CustomerForm extends AbstractRegistrationForm
{


    /** @var Customer|null */
    private $customer;

    /** @var CustomerStorageFacadeFactory */
    private $customerFacadeFactory;



    public function __construct(BirthdayFormContainer $birthdayFormContainer,
                                Context $database,
                                CustomerStorageFacadeFactory $customerStorageFacadeFactory,
                                ITranslator $translator)
    {
        parent::__construct($birthdayFormContainer, $database, $translator);
        $this->customerFacadeFactory = $customerStorageFacadeFactory;
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
     * @inheritdoc
     * @throws \InvalidArgumentException
     */
    public function createComponentForm() : Form
    {
        $form = parent::createComponentForm();

        //inputs
        $form->removeComponent($form->getComponent('contact'));
        $form->addSubmit('submit', $this->translator->translate('form.customerForm.input.submit.label'));

        //condition for birthday form
        $birthdayYear = $form->getComponent(BirthdayFormContainer::NAME)->getComponent('year');
        $birthdayDay = $form->getComponent(BirthdayFormContainer::NAME)->getComponent('day');
        $this->birthdayForm['month']->addConditionOn($birthdayYear, Form::FILLED, TRUE)
            ->setRequired($this->translator->translate('form.birthday.error.month'))
            ->elseCondition()
            ->addConditionOn($birthdayDay, Form::FILLED, TRUE)
            ->setRequired($this->translator->translate('form.birthday.error.month'));

        $this->setDefaultFormValues($form);

        return $form;
    }



    /**
     * @inheritdoc
     * @throws \InvalidArgumentException
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $values->postCode = str_replace(' ', '', $values->postCode);
        $presenter = $this->getPresenter();
        $customer = $this->getCustomer();

        try {
            $this->database->beginTransaction();
            $customerFacade = $this->customerFacadeFactory->create();
            $customerFacade->save($customer->getId(), $values->email, $values->name, $values->surname,
                $values->sex, $customer->getExternalSystemId(), $customer->getPassword(), $values->telephone ?: NULL,
                $customer->getAddressing(), $values->street ?: NULL, $values->city ?: NULL, (int)$values->postCode ?: NULL,
                $values->state ?: NULL, (int)$values->{BirthdayFormContainer::NAME}->year ?: NULL, (int)$values->{BirthdayFormContainer::NAME}->month ?: NULL,
                (int)$values->{BirthdayFormContainer::NAME}->day ?: NULL, $customer->hasBirthdayCoupon(), $customer->wantNewsletter(),
                $customer->getExternalSystemLastChangeDate(), $customer->getState());
            $this->database->commit();

            $presenter->flashMessage($this->translator->translate('form.customerForm.message.success'), 'success');
            $presenter->redirect('this');
        } catch (CustomerStorageException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    /**
     * @return Customer
     * @throws \InvalidArgumentException customer is missing
     */
    public function getCustomer() : Customer
    {
        if (!$this->customer instanceof Customer) {
            throw new \InvalidArgumentException('Missing customer.');
        }
        return $this->customer;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/templates/default.latte');
        $this->template->render();
    }



    /**
     * @param $form Form
     * @return Form
     */
    private function setDefaultFormValues(Form $form) : Form
    {
        $customer = $this->getCustomer();
        $form->setDefaults([
            'name' => $customer->getFirstName(),
            'surname' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'sex' => $customer->getSex(),
            BirthdayFormContainer::NAME => [
                'day' => $customer->getBirthdayDay(),
                'month' => $customer->getBirthdayMonth(),
                'year' => $customer->getBirthdayYear(),
            ],
            'street' => $customer->getStreet(),
            'city' => $customer->getCity(),
            'postCode' => $customer->getPostcode(),
            'state' => $customer->getCountryCode(),
            'telephone' => $customer->getTelephone(),
        ]);

        return $form;
    }
}