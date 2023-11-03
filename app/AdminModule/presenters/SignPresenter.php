<?php

namespace App\AdminModule\Presenters;

use App\Components\SignInForm\SignInForm;
use App\Components\SignInForm\SignInFormFactory;
use App\FacadeException;
use Nette\Application\UI\Form;


class SignPresenter extends BasePresenter
{


    /** @var string|null @persistent */
    public $backlink;

    /** @var SignInFormFactory @inject */
    public $signInFormFactory;



    public function startup()
    {
        parent::startup();
        $this->setLayout(__DIR__ . "/templates/@layoutBeforeAdministration.latte");
    }



    /**
     * Sign in
     * @return void
     */
    public function renderIn()
    {
        try {
            if ($this->userFacadeFactory->create()->getUserLoggedIdentity($this->getUser())) {
                $this->redirect("Homepage:default");
            }
        } catch (FacadeException $exception) {

        }
    }



    /**
     * Sign out
     * @return void
     */
    public function actionOut()
    {
        $this->userFacadeFactory->create()->logout($this->getUser());
        $this->flashMessage("Byli jste úspěšně odhlášeni.", "success");
        $this->redirect("Sign:in");
    }



    /**
     * @return SignInForm
     */
    public function createComponentSignInForm()
    {
        $form = $this->signInFormFactory->create();
        $form->onSuccess(function (Form $form) {
            $values = $form->getValues();
            try {
                $this->database->beginTransaction();
                $user = $this->userFacadeFactory->create()->login($values->email, $values->password, $this->getUser());
                $this->database->commit();

                //log
                $this->logger->addInfo(sprintf('admin.sign.in.form: Přihlášení uživatele administrace s id \'%d\'.', $user->getIdentity()->getEntity()->getId()), ['route' => $this->getAction(TRUE)]);

                $this->flashMessage("Přihlášení proběhlo v pořádku.", "success");
                $this->restoreRequest($this->backlink);
                $this->redirect("Homepage:default");

            } catch (FacadeException $exception) {
                $this->database->rollBack();
                $this->flashMessage($exception->getMessage(), "danger");
                $this->redirect("this");
            }
        });
        return $form;
    }
}