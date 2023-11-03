<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Components\PasswordForm\PasswordForm;
use App\Components\PasswordForm\PasswordFormFactory;
use App\Customer\CustomerStorageException;
use App\Customer\CustomerStorageFacadeFactory;
use App\FrontModule\Components\Breadcrumb\Item;
use App\FrontModule\Components\Customer\NewsletterForm\NewsletterForm;
use App\FrontModule\Components\Customer\NewsletterForm\NewsletterFormFactory;
use App\FrontModule\Components\Customer\OrderList\OrderList;
use App\FrontModule\Components\Customer\OrderList\OrderListFactory;
use App\FrontModule\Components\CustomerForm\CustomerForm;
use App\FrontModule\Components\CustomerForm\CustomerFormFactory;
use App\FrontModule\Components\Order\RelatedProduct\RelatedProduct;
use App\FrontModule\Components\Order\RelatedProduct\RelatedProductFactory;
use App\Order\Order;
use App\Order\OrderNotFoundException;
use App\Order\OrderRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class AccountPresenter extends AbstractLoggedPresenter
{


    /** @var CustomerStorageFacadeFactory @inject */
    public $customerFacadeFactory;

    /** @var CustomerFormFactory @inject */
    public $customerFormFactory;

    /** @var NewsletterFormFactory @inject */
    public $customerNewsletterFormFactory;

    /** @var Order|null */
    private $order;

    /** @var OrderListFactory @inject */
    public $orderListFactory;

    /** @var OrderRepository @inject */
    public $orderRepo;

    /** @var PasswordFormFactory @inject */
    public $passwordFormFactory;

    /** @var RelatedProductFactory @inject */
    public $relatedProductFactory;



    /**
     * @inheritdoc
     * @throws AbortException
     */
    public function startup()
    {
        parent::startup();

        $this->breadcrumb->addItem(new Item($this->translator->translate('header.menu.account.title'), $this->link('Account:default')));
        $this->template->index = FALSE;
    }



    /**
     * Action "default"
     */
    public function actionDefault()
    {
        $title = $this->translator->translate('account.menu.overview.label');
        $this->breadcrumb->addItem(new Item($title));

        $this->template->lastOrder = $this->orderRepo->findOneLastByCustomerId($this->loggedUser->getEntity()->getId());
        $this->template->title = $title;
        $this->template->translator = $this->translator;
    }



    /**
     * @param $code string
     * @throws BadRequestException
     */
    public function actionOrderDetail(string $code)
    {
        try {
            $order = $this->orderRepo->getOneByCodeAndCustomerId($code, $this->loggedUser->getId());

            //breadcrumb
            $title = $this->translator->translate('presenterFront.account.order') . ' ' . $order->getCode();
            $this->breadcrumb->addItem(new Item($this->translator->translate('account.menu.orders.label'), $this->link('Account:orderList')));
            $this->breadcrumb->addItem(new Item($title));

            $this->template->order = $this->order = $order;
            $this->template->title = $title;
        } catch (OrderNotFoundException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
     * @return void
     */
    public function actionOrderList()
    {
        $title = $this->translator->translate('account.menu.orders.label');
        $this->breadcrumb->addItem(new Item($title));

        $this->template->title = $title;
    }



    /**
     * @return void
     */
    public function actionPasswordChange()
    {
        $title = $this->translator->translate('account.menu.passwordChange.label');
        $this->breadcrumb->addItem(new Item($title));

        $this->template->title = $title;
    }



    /**
     * @return void
     */
    public function actionPersonalData()
    {
        $title = $this->translator->translate('account.menu.personalData.label');
        $this->breadcrumb->addItem(new Item($title));

        $this->template->title = $title;
    }



    /**
     * @return CustomerForm
     */
    public function createComponentCustomerForm() : CustomerForm
    {
        $form = $this->customerFormFactory->create();
        $form->setCustomer($this->loggedUser->getEntity());
        return $form;
    }



    /**
     * @return NewsletterForm
     */
    public function createComponentCustomerNewsletterForm() : NewsletterForm
    {
        $form = $this->customerNewsletterFormFactory->create();
        $form->setCustomer($this->loggedUser->getEntity());
        return $form;
    }



    /**
     * @return OrderList
     */
    public function createComponentOrderList() : OrderList
    {
        $list = $this->orderListFactory->create();
        $list->setCustomer($this->loggedUser->getEntity());
        return $list;
    }



    /**
     * @return RelatedProduct
     */
    public function createComponentRelatedProduct() : RelatedProduct
    {
        $list = $this->relatedProductFactory->create();
        $list->setCustomer($this->loggedUser->getEntity());
        $list->setOrder($this->order);
        return $list;
    }



    /**
     * @return PasswordForm
     * @throws AbortException
     */
    public function createComponentPasswordForm() : PasswordForm
    {
        $form = $this->passwordFormFactory->create();
        $form->setPasswordActual(TRUE);
        $form->onSuccess(function (Form $form) {
            try {
                $values = $form->getValues();

                $this->database->beginTransaction();
                $customerFacade = $this->customerFacadeFactory->create();
                $customerFacade->changePassword($this->loggedUser->getEntity()->getId(), $values->passwordActual, $values->password);
                $this->database->commit();

                $this->flashMessage($this->translator->translate('form.newPassword.message.successChangedPassword'), 'success');
                $this->redirect('this');
            } catch (CustomerStorageException $exception) {
                $this->database->rollBack();
                $this->flashMessage($exception->getMessage(), 'danger');
            }
        });
        return $form;
    }
}