<?php

namespace App\Components\PasswordForm;

use Kdyby\Translation\ITranslator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PasswordForm extends Control
{


    /** @var callable|null */
    protected $onSuccess;

    /** @var bool */
    protected $passwordActual = false;

    /** @var int|null */
    protected $passwordMinimumLength;

    /** @var ITranslator */
    protected $translator;



    public function __construct(ITranslator $translator)
    {
        parent::__construct();
        $this->translator = $translator;
    }



    /**
     * @param $callback callable
     * @return self
     */
    public function onSuccess(callable $callback)
    {
        $this->onSuccess = $callback;
        return $this;
    }



    /**
     * @param $param bool true is required actual password
     * @return self
     */
    public function setPasswordActual(bool $param)
    {
        $this->passwordActual = $param;
        return $this;
    }



    /**
     * @param int $passwordMinimumLength
     * @return self
     */
    public function setPasswordMinimumLength(int $passwordMinimumLength) : self
    {
        $this->passwordMinimumLength = $passwordMinimumLength;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentNewPasswordForm()
    {
        $form = new Form();
        if ($this->passwordActual) {
            $form->addPassword("passwordActual", $this->translator->translate('form.newPassword.input.actualPassword.label') . '*')
                ->setRequired($this->translator->translate('form.newPassword.input.actualPassword.require'))
                ->setAttribute("class", "form-control")
                ->setAttribute('autofocus');
        }
        $password = $form->addPassword("password", $this->translator->translate('form.newPassword.input.password.label') . '*')
            ->setRequired($this->translator->translate('form.newPassword.input.password.require'))
            ->setAttribute("class", "form-control")
            ->setAttribute('autofocus');
        $form->addPassword("passwordConfirm", $this->translator->translate('form.newPassword.input.passwordConfirm.label') . '*')
            ->setAttribute("class", "form-control")
            ->setRequired($this->translator->translate('form.newPassword.input.passwordConfirm.require'))
            ->addRule(Form::EQUAL, $this->translator->translate('form.registration.error.passwordsAreNotEqual'), $form["password"]);
        $form->addSubmit("submit", $this->translator->translate('form.newPassword.input.submit.label'))
            ->setAttribute("class", "btn btn-primary");
        $form->onSuccess[] = $this->onSuccess;

        if ($this->passwordMinimumLength !== NULL) {
            $password->addRule(Form::MIN_LENGTH, $this->translator->translate('form.newPassword.input.password.minimumLength', ['length' => $this->passwordMinimumLength]), $this->passwordMinimumLength);
        }

        return $form;
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . '/templates/default.latte');
        $this->template->passwordActual = $this->passwordActual;
        $this->template->render();
    }



    /**
     * @return void
     */
    public function renderFrontend()
    {
        $this->template->setFile(__DIR__ . '/templates/frontend.latte');
        $this->template->render();
    }



    /**
     * @return void
     */
    public function renderFrontendChange()
    {
        $this->template->setFile(__DIR__ . '/templates/frontendChange.latte');
        $this->template->render();
    }



    /**
     * @return void
     */
    public function renderFrontendStoreRegistrationCompletion()
    {
        $this->template->setFile(__DIR__ . '/templates/frontendStoreRegistrationCompletion.latte');
        $this->template->render();
    }
}