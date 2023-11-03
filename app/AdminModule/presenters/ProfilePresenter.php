<?php

namespace App\AdminModule\Presenters;

use App\Components\PasswordForm\PasswordForm;
use App\Components\PasswordForm\PasswordFormFactory;
use App\FacadeException;
use App\User\UserEntity;
use Nette\Application\UI\Form;


class ProfilePresenter extends AdminModulePresenter
{


    /** @var PasswordFormFactory @inject */
    public $passwordFormFactory;



    /**
     * Render default
     * @return void
     */
    public function renderDefault()
    {
        $this->template->title = "Profil";
    }



    /**
     * @return PasswordForm
     */
    public function createComponentPasswordForm()
    {
        $form = $this->passwordFormFactory->create();
        $form->setPasswordActual(true);
        $form->setPasswordMinimumLength(UserEntity::PASSWORD_MIN_LENGTH);
        $form->onSuccess(function (Form $form) {
            try {
                $values = $form->getValues();
                $this->database->beginTransaction();
                $this->userFacadeFactory->create()->saveNewPassword($this->getUser()->getId(), $values->password, $values->passwordActual);
                $this->database->commit();
                $this->flashMessage("Heslo bylo změněno.", "success");
                $this->redirect("this");
            } catch (FacadeException $exception) {
                $this->database->rollBack();
                $this->flashMessage($exception->getMessage(), "danger");
            }
        });
        return $form;
    }
}