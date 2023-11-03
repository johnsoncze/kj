<?php

namespace App\AdminModule\Presenters;

use App\Components\ForgottenPasswordForm\ForgottenPasswordForm;
use App\Components\ForgottenPasswordForm\ForgottenPasswordFormFactory;
use App\Components\PasswordForm\PasswordForm;
use App\Components\PasswordForm\PasswordFormFactory;
use App\FacadeException;
use App\ForgottenPassword\ForgottenPasswordFacadeFactory;
use App\User\UserEntity;
use App\User\UserFacadeFactory;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;


class PasswordPresenter extends BasePresenter
{


    /** @var UserFacadeFactory @inject */
    public $userFacadeFactory;

    /** @var ForgottenPasswordFacadeFactory @inject */
    public $forgottenPasswordFacadeFactory;

    /** @var PasswordFormFactory @inject */
    public $passwordFormFactory;

    /** @var ForgottenPasswordFormFactory @inject */
    public $forgottenPasswordFormFactory;

    /** @var ForgottenPasswordFacadeFactory @inject */
    public $facadeFactory;



    public function startup()
    {
        parent::startup();
        $this->setLayout(__DIR__ . "/templates/@layoutBeforeAdministration.latte");
    }



    /**
     * @return void
     */
    public function renderForgotten()
    {
        try {
            if ($this->userFacadeFactory->create()->getUserLoggedIdentity($this->getUser())) {
                $this->flashMessage("Jste již přihlášeni do administrace. Pro změnu hesla využijte nastavení.", "info");
                $this->redirect("Homepage:default");
            }
        } catch (FacadeException $exception) {
        }
    }



    /**
     * @param $userId int
     * @param $hash string
     * @return void
     * @throws BadRequestException
     */
    public function renderNew($userId, $hash)
    {
        try {
            $request = $this->forgottenPasswordFacadeFactory
                ->create()
                ->getUserValidRequest($userId, $hash);
        } catch (FacadeException $exception) {
            $this->flashMessage($exception->getMessage(), "info");
            $this->redirect("Password:forgotten");
        }
    }



    /**
     * @return ForgottenPasswordForm
     */
    public function createComponentForgottenPasswordForm()
    {
        $form = $this->forgottenPasswordFormFactory->create();
        $form->onSuccess(function (Form $form) {
            $values = $form->getValues();
            try {
                $this->database->beginTransaction();
                $this->facadeFactory
                    ->create()
                    ->addNewForUser($values->email);
                $this->database->commit();
                $this->presenter->flashMessage("Žádost byla odeslána. Zkontrolujte svoji e-mailovou schránku.", "success");
                $this->presenter->redirect("this");
            } catch (FacadeException $exception) {
                $this->database->rollback();
                $this->presenter->flashMessage($exception->getMessage(), "danger");
                $this->presenter->redirect("this");
            }
        });
        return $form;
    }



    /**
     * @return PasswordForm
     */
    public function createComponentPasswordForm()
    {
        $form = $this->passwordFormFactory->create();
        $form->setPasswordMinimumLength(UserEntity::PASSWORD_MIN_LENGTH);
        $form->onSuccess(function (Form $form) {
            try {
                $values = $form->getValues();
                $userId = $this->getParameter("userId");
                $this->database->beginTransaction();
                $this->userFacadeFactory->create()->saveNewPassword($userId, $values->password);
                $this->forgottenPasswordFacadeFactory->create()->removeUserRequests($userId);
                $this->database->commit();
                $this->flashMessage("Heslo bylo nastaveno. Nyní se můžete přihlásit.", "success");
                $this->redirect("Sign:in");
            } catch (FacadeException $exception) {
                $this->database->rollback();
                $this->flashMessage($exception->getMessage(), "danger");
                $this->redirect("this");
            }
        });
        return $form;
    }
}