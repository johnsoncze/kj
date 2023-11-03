<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Components\ForgottenPasswordForm\ForgottenPasswordForm;
use App\Components\ForgottenPasswordForm\ForgottenPasswordFormFactory;
use App\Components\PasswordForm\PasswordForm;
use App\Components\PasswordForm\PasswordFormFactory;
use App\Customer\Activation\ActivationFacadeException;
use App\Customer\Activation\ActivationFacadeFactory;
use App\Customer\Activation\Request;
use App\Customer\CustomerStorageException;
use App\Customer\CustomerStorageFacadeFactory;
use App\FacadeException;
use App\ForgottenPassword\ForgottenPasswordEntity;
use App\ForgottenPassword\ForgottenPasswordFacadeException;
use App\ForgottenPassword\ForgottenPasswordFacadeFactory;
use App\ShoppingCart\ShoppingCartSaveFacadeException;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SignPresenter extends AbstractPresenter
{


    /** @var string|null @persistent */
    public $_b;

    /** @var ActivationFacadeFactory @inject */
    public $activationFacadeFactory;

    /** @var Request|null */
    public $activationRequest;

    /** @var CustomerStorageFacadeFactory @inject */
    public $customerStorageFacadeFactory;

    /** @var ForgottenPasswordFacadeFactory @inject */
    public $forgottenPasswordFacadeFactory;

    /** @var ForgottenPasswordFormFactory @inject */
    public $forgottenPasswordFormFactory;

    /** @var PasswordFormFactory @inject */
    public $passwordFormFactory;

    /** @var ForgottenPasswordEntity|null */
    private $forgottenPassword;



    /**
     * @return void
     * @throws AbortException
     */
    public function actionIn()
    {
        $this->redirectLoggedUser();

        $this->template->title = $this->translator->translate('account.sign.in.title');
    }



    /**
     * @return void
     * @throws AbortException
     */
    public function actionForgottenPassword()
    {
        $this->redirectLoggedUser();

        $this->template->title = $this->translator->translate('account.sign.password.forgotten.title');
    }



    /**
     * Action "setNewPassword". After forgotten password.
     * @param $token string
     * @param $email string
     * @throws BadRequestException
     * @throws AbortException
     */
    public function actionSetNewPassword(string $token, string $email)
    {
        $this->redirectLoggedUser();

        try {
            $forgottenPassword = $this->forgottenPasswordFacadeFactory->create();
            $this->forgottenPassword = $forgottenPassword->validateCustomerRequest($email, $token);
        } catch (FacadeException $exception) {
            $this->flashMessage(
                $this->translator->translate('presenterFront.account.passwordResetTokenInvalid'),
                'danger'
            );
            $this->redirect('forgottenPassword');
        } catch (\Exception $exception) {
            throw new BadRequestException(NULL, 404);
        }

        $this->template->title = $this->translator->translate('presenterFront.account.setNewPassword');
    }



    /**
     * @param $token string
     * @param $email string
     * @return void
     * @throws AbortException
     * @throws BadRequestException
     */
    public function actionStoreRegistration(string $token, string $email)
    {
        $this->redirectLoggedUser();

        try {
            $activationFacade = $this->activationFacadeFactory->create();
            $this->activationRequest = $activationFacade->validateRequest($email, $token);
        } catch (ActivationFacadeException $exception) {
            if ($exception->getCode() === ActivationFacadeException::EXPIRED) {
                $this->flashMessage($this->translator->translate('customer.activation.expired'), 'info');
                $this->redirect('Sign:storeRegistrationRequest');
            }
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
     * @return void
    */
    public function actionStoreRegistrationRequest()
    {
        $this->template->title = $this->translator->translate('presenterFront.account.storeRegistrationRequest');
    }



    /**
     * Sign out.
     * @return void
     * @throws AbortException
     */
    public function actionOut()
    {
        $user = $this->getUser();
        $user->getStorage()->setNamespace(AbstractPresenter::USER_IDENTITY_NAMESPACE);
        $user->logout(TRUE);

        try {
            if ($this->shoppingCart) {
                $this->database->beginTransaction();
                $this->shoppingCartSaveFacade->removeCustomer($this->shoppingCart->getEntity()->getId());
                $this->database->commit();
            }
        } catch (ShoppingCartSaveFacadeException $exception) {
            $this->database->rollBack();
            $this->logger->addError($exception->getMessage());
        }

        $this->flashMessage($this->translator->translate('form.sign.out.message.success'), 'success');
        $this->restoreRequest(self::BACKLINK);
        $this->redirect('Sign:in');
    }



    /**
     * @return void
    */
    public function actionUp()
    {
        $this->template->title = $this->translator->translate('account.sign.up.title');
    }



    /**
     * @return ForgottenPasswordForm
     * @throws AbortException
     */
    public function createComponentForgottenPasswordForm() : ForgottenPasswordForm
    {
        $form = $this->forgottenPasswordFormFactory->create();
        $form->onSuccess(function (Form $form) {
            $values = $form->getValues();
            $presenter = $this->presenter;

            try {
                $this->database->beginTransaction();
                $facade = $this->forgottenPasswordFacadeFactory->create();
                $facade->addRequestForCustomer($values->email);
                $this->database->commit();
            } catch (ForgottenPasswordFacadeException $exception) {
                $this->database->rollBack();

                //if customer is not activated yet, send request for activation instead of request of forgotten password
                if ($exception->getCode() === ForgottenPasswordFacadeException::NOT_ACTIVATED) {
                    try {
                        $this->database->beginTransaction();
                        $activationFacade = $this->activationFacadeFactory->create();
                        $activationFacade->createRequest($values->email);
                        $this->database->commit();
                    } catch (ActivationFacadeException $exception) {
                        $this->database->rollBack();
                    }
                }
            }

            $presenter->flashMessage($this->translator->translate('form.forgottenPassword.message.success'), 'success');
            $presenter->redirect('this');
        });
        return $form;
    }



    /**
     * @return PasswordForm
     * @throws AbortException
     * @throws BadRequestException
     */
    public function createComponentPasswordForm() : PasswordForm
    {
        $form = $this->passwordFormFactory->create();
        $form->onSuccess(function (Form $form) {
            $values = $form->getValues();
            $presenter = $this->presenter;
            $customerId = (int)$this->forgottenPassword->getCustomerId();

            try {
                $this->database->beginTransaction();

                //set new password
                $customerStorage = $this->customerStorageFacadeFactory->create();
                $customerStorage->setPassword($customerId, $values->password);

                //remove customer forgotten password requests
                $forgottenPasswordFacade = $this->forgottenPasswordFacadeFactory->create();
                $forgottenPasswordFacade->removeCustomerRequests($customerId);
                $this->database->commit();

                $presenter->flashMessage($this->translator->translate('form.newPassword.message.successAfterForgotten'), 'success');
                $presenter->redirect('Sign:in');
            } catch (CustomerStorageException $exception) {
                $this->database->rollBack();
                throw new BadRequestException(NULL, 404);
            }
        });
        return $form;
    }



    /**
     * @return PasswordForm
     * @throws AbortException
     * @throws BadRequestException
     */
    public function createComponentStoreRegistrationForm() : PasswordForm
    {
        $form = $this->passwordFormFactory->create();
        $form->onSuccess(function (Form $form) {
            $values = $form->getValues();
            $presenter = $this->presenter;

            try {
                $this->database->beginTransaction();
                $activationFacade = $this->activationFacadeFactory->create();
                $activationFacade->activate($this->activationRequest->getEmail(), $this->activationRequest->getToken(), $values->password);
                $this->database->commit();

                $presenter->flashMessage($this->translator->translate('customer.activation.success'), 'success');
                $presenter->redirect('Sign:in');
            } catch (ActivationFacadeException $exception) {
                $this->database->rollBack();
                throw new BadRequestException(NULL, 404);
            }
        });
        return $form;
    }



    /**
     * @return ForgottenPasswordForm
     * @throws AbortException
     */
    public function createComponentStoreRegistrationRequestForm() : ForgottenPasswordForm
    {
        $form = $this->forgottenPasswordFormFactory->create();
        $form->onSuccess(function (Form $form) {
            $values = $form->getValues();
            $presenter = $this->getPresenter();

            try {
                $this->database->beginTransaction();
                $activationFacade = $this->activationFacadeFactory->create();
                $activationFacade->createRequest($values->email);
                $this->database->commit();

                $presenter->flashMessage($this->translator->translate('customer.activation.request'), 'success');
                $presenter->redirect('this');
            } catch (ActivationFacadeException $exception) {
                $this->database->rollBack();
                $presenter->flashMessage($this->translator->translate('customer.activation.request.error'), 'danger');
            }
        });
        return $form;
    }



    /**
     * If is user logged, redirect him to default page of account.
     * @throws AbortException
     */
    protected function redirectLoggedUser()
    {
        if ($this->loggedUser !== NULL) {
            if($this->_b)
                $this->restoreRequest($this->_b);
            $this->redirect('Account:default');
        }
    }
}