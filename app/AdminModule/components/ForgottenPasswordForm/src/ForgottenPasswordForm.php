<?php

namespace App\Components\ForgottenPasswordForm;

use Kdyby\Translation\ITranslator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ForgottenPasswordForm extends Control
{


    /** @var Context */
    protected $database;

    /** @var callable */
    protected $onSuccess;

    /** @var ITranslator */
    protected $translator;



    public function __construct(Context $context,
                                ITranslator $translator)
    {
        parent::__construct();
        $this->database = $context;
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
     * @return Form
     */
    public function createComponentForgottenPasswordForm()
    {
        $form = new Form();
        $form->addText("email", "E-mail*")
            ->setRequired($this->translator->translate('form.forgottenPassword.input.email.require'))
            ->addRule(Form::EMAIL, $this->translator->translate('general.error.invalidEmailFormat'))
            ->setAttribute("class", "form-control");
        $form->addSubmit("submit", $this->translator->translate('form.forgottenPassword.input.submit.label'))
            ->setAttribute("class", "btn btn-primary");
        $form->onSuccess[] = $this->onSuccess;
        return $form;
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . '/templates/default.latte');
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
    public function renderFrontendStoreRegistrationRequest()
    {
        $this->template->setFile(__DIR__ . '/templates/frontendStoreRegistrationRequest.latte');
        $this->template->render();
    }
}