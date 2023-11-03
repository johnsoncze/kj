<?php

declare(strict_types=1);

namespace App\FrontModule\Components\Registration\Form;

use App\Customer\Customer;
use App\FrontModule\Components\BirthdayFormContainer\BirthdayFormContainer;
use App\Helpers\Regex;
use App\Location\State;
use Kdyby\Translation\ITranslator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Nette\InvalidStateException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AbstractRegistrationForm extends Control
{


    /** @var BirthdayFormContainer */
    protected $birthdayForm;

    /** @var Context */
    protected $database;

    /** @var ITranslator */
    protected $translator;


    public function __construct(
        BirthdayFormContainer $birthdayFormContainer,
        Context $database,
        ITranslator $translator
    ) {
        parent::__construct();
        $this->birthdayForm = $birthdayFormContainer;
        $this->database = $database;
        $this->translator = $translator;
    }


    /**
     * @return Form
     * @throws InvalidStateException
     */
    public function createComponentForm(): Form
    {
        $form = new Form();

        //general information
        $form->addText('name', $this->translator->translate('form.registration.label.name') . '*', null, 50)
            ->setRequired($this->translator->translate('form.registration.error.name'));
        $form->addText('surname', $this->translator->translate('form.registration.label.surname') . '*', null, 50)
            ->setRequired($this->translator->translate('form.registration.error.surname'));
        $form->addText('email', 'E-mail*')
            ->setRequired($this->translator->translate('form.registration.error.email') . '*')
            ->addRule(Form::EMAIL, $this->translator->translate('general.error.invalidEmailFormat'))
            ->setMaxLength(50);
        //additional information
        $form->addRadioList('sex', $this->translator->translate('form.registration.label.sex') . '*', Customer::getSexList())
            ->setRequired($this->translator->translate('form.registration.error.sex'));

        //birthday
        $form->addCheckbox('contact', $this->translator->translate('form.registration.label.contact'));
        $form->addComponent($this->birthdayForm, BirthdayFormContainer::NAME);

        //contact
        $form->addText('street', $this->translator->translate('form.contact.label.street'))
            ->setMaxLength(35);
        $form->addText('city', $this->translator->translate('form.contact.label.city'))
            ->setMaxLength(35);
        $form->addText('postCode', $this->translator->translate('form.contact.label.postCode'))
            ->setMaxLength(6)
            ->addCondition(Form::FILLED)
            ->addRule(Form::PATTERN, $this->translator->translate('form.contact.error.postCode'), Regex::POSTCODE);

        $form->addSelect('state', $this->translator->translate('form.contact.label.state'), State::getList($this->translator))
            ->setPrompt($this->translator->translate('form.general.selectbox.prompt'))
            ->setAttribute('class', 'js-selectfield');
        $form->addText('telephone', $this->translator->translate('form.contact.label.telephone'))
            ->setMaxLength(20);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }


    /**
     * Handler for success sent form.
     * @param $form Form
     */
    public abstract function formSuccess(Form $form);
}