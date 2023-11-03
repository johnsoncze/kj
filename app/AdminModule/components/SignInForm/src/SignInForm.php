<?php

namespace App\Components\SignInForm;

use App\User\UserFacadeFactory;
use Kdyby\Translation\ITranslator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SignInForm extends Control
{


    /** @var Context */
    protected $database;

    /** @var UserFacadeFactory */
    protected $userFacadeFactory;

    /** @var callable */
    protected $onSuccess;

    /** @var ITranslator */
    protected $translator;



    public function __construct(Context $context,
                                ITranslator $translator,
                                UserFacadeFactory $userFacadeFactory)
    {
        parent::__construct();
        $this->database = $context;
        $this->translator = $translator;
        $this->userFacadeFactory = $userFacadeFactory;
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
     * Login form
     * @return Form
     */
    public function createComponentLoginForm()
    {
        $form = new Form();
        $form->addText("email", "E-mail*")
            ->setAttribute("autofocus", TRUE)
            ->setRequired($this->translator->translate('form.sign.in.input.email.require'))
            ->addRule(Form::EMAIL, $this->translator->translate('general.error.invalidEmailFormat'))
            ->setAttribute("class", "form-control");
        $form->addPassword("password", $this->translator->translate('form.sign.in.input.password.label') . '*')
            ->setRequired($this->translator->translate('form.sign.in.input.password.require'))
            ->setAttribute("class", "form-control");
        $form->addSubmit("submit", $this->translator->translate('form.sign.in.input.submit.label'))
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
    public function renderFrontendDefault()
    {
        $this->template->setFile(__DIR__ . '/templates/frontendDefault.latte');
        $this->template->render();
    }
}