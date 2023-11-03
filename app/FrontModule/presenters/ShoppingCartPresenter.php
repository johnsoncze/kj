<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Category\CategoryFindFacadeFactory;
use App\Category\Product\Related\ProductFacadeFactory;
use App\Delivery\DeliveryRepository;
use App\FrontModule\Components\Ecomail\EcomailHelper;
use App\FrontModule\Components\OpportunityForm\Data;
use App\FrontModule\Components\OpportunityForm\OpportunityForm;
use App\FrontModule\Components\OpportunityForm\Product;
use App\FrontModule\Components\ShoppingCart\Benefit\Benefit;
use App\FrontModule\Components\ShoppingCart\Benefit\BenefitFactory;
use App\FrontModule\Components\ShoppingCart\ButtonNavigation\ButtonNavigation;
use App\FrontModule\Components\ShoppingCart\ButtonNavigation\ButtonNavigationFactory;
use App\FrontModule\Components\ShoppingCart\ContactInformationForm\ContactInformationForm;
use App\FrontModule\Components\ShoppingCart\ContactInformationForm\ContactInformationFormFactory;
use App\FrontModule\Components\ShoppingCart\DeliveryForm\DeliveryForm;
use App\FrontModule\Components\ShoppingCart\DeliveryForm\DeliveryFormFactory;
use App\Google\TagManager\DataLayer;
use App\Google\TagManager\EnhancedEcommerce\DataFactory;
use App\Helpers\Entities;
use App\Opportunity\Opportunity;
use App\Opportunity\Product\ProductStorageFacadeException;
use App\Order\OrderCreateFacadeFactory;
use App\Order\OrderFacadeFactory;
use App\Order\OrderNotFoundException;
use App\Order\OrderRepository;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockFacadeFactory;
use App\Remarketing\Code\CodeDTO;
use App\ShoppingCart\ShoppingCartDTO;
use App\ShoppingCart\ShoppingCartSaveFacadeException;
use App\ShoppingCart\Validity\ValidityFacadeException;
use App\ShoppingCart\Validity\ValidityFacadeFactory;
use Nette\Application\AbortException;
use Nette\Utils\Json;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ShoppingCartPresenter extends AbstractPresenter
{

    /** @var string|null @persistent */
    public $_b;

    /** @var BenefitFactory @inject */
    public $benefitFactory;

    /** @var ButtonNavigationFactory @inject */
    public $buttonNavigationFactory;

    /** @var CategoryFindFacadeFactory @inject */
    public $categoryFindFacadeFactory;

    /** @var ContactInformationFormFactory @inject */
    public $contactInformationFormFactory;

    /** @var DeliveryFormFactory @inject */
    public $deliveryFormFactory;

    /** @var LockFacadeFactory @inject */
    public $parameterLockFacadeFactory;

    /** @var OrderFacadeFactory @inject */
    public $orderFacadeFactory;

    /** @var OrderRepository @inject */
    public $orderRepository;

    /** @var DeliveryRepository @inject */
    public $deliveryRepository;

    /** @var ProductFacadeFactory @inject */
    public $productFacadeFactory;

    /** @var ValidityFacadeFactory @inject */
    public $validityFacadeFactory;

    /** @var EcomailHelper @inject */
    public $ecomailHelper;

    /** @var OrderCreateFacadeFactory @inject */
    public $orderCreateFacadeFactory;

    /** @var DataLayer @inject */
    public $dataLayer;


    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->activeStep = 1;
    }


    /**
     * @inheritdoc
     */
    public function startup()
    {
        parent::startup();
        $this->remarketingCode->setPageType(CodeDTO::PAGE_TYPE_CART);
        if ($this->shoppingCart && $this->shoppingCart->hasProducts() && $this->getAction() !== 'step4') {
            $this->remarketingCode->setData([
                CodeDTO::DATA_TOTALVALUE_KEY => (float)number_format($this->shoppingCart->getPrice()->productSummaryPriceWithoutVat, 2, '.', ''),
            ]);
        }

        $this->template->index = false;
        $this->template->title = $this->translator->translate('shopping-cart.step.' . $this->getAction() . '.title');
        $this->template->action = $this->getAction();

        //template parts
        $this->template->templateNavigation = false;
        $this->template->templateFooter = false;
        $this->template->templateFooterSimple = true;
    }


    public function renderStep1()
    {
				$this->checkBirthdayCoupon();
        $this->template->activeStep = 1;
    }		
		
    /**
     * @return void
     * @throws AbortException
     */
    public function actionStep1links()
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect('ShoppingCart:step3');
        }
//        $this->_b = $this->storeRequest();
    }

    /**
     * @return void
     * @throws AbortException
     */
    public function renderStep1links()
    {
        $this->_b = $this->storeRequest();
        $this->template->activeStep = 2;
    }

    /**
     * @return void
     * @throws AbortException
     */
    public function actionStep2()
    {
        $this->checkShoppingCart();
        $this->checkShoppingCartProducts();
    }

    /**
     * @return void
     * @throws AbortException
     */
    public function renderStep2()
    {
        $this->template->activeStep = 2;

        $cartPrice = $this->shoppingCart->getPrice();
        $codAllowed = (!$this->shoppingCart->hasNonStockableProducibleProduct() && $cartPrice->summaryPrice <= 50000) ? '7' : null;
        //1 online (kartou/expres prevod)
        //3 hotovost
        //5 kartou osobne
        //6 prevod
        //7 dobirka

        $allowedPayments = [
            1 => ['1', '6', $codAllowed,],
            2 => ['1', '6', $codAllowed,],
            3 => ['1', '6', $codAllowed,],
            4 => ['1', '6', $codAllowed,],
            5 => ['1', '3', '5', '6',],     //osobni odber
        ];
        if ($this->shoppingCart->hasNonStockableProducibleProduct()) {
            $allowedPayments[5] = ['1', '6',];
        }
        $this->template->allowed = Json::encode($allowedPayments);
    }


    public function renderStep3()
    {
        $this->template->activeStep = 3;
    }


    /**
     * @return void
     * @throws AbortException
     */
    public function actionStep3()
    {
        $this->checkShoppingCart();
        $this->checkShoppingCartProducts();
        $this->checkDelivery();
        $this->checkPayment();
        $this->validateShoppingCart($this->shoppingCart);
    }

    /**
     * @param $order string
     * @return void
     */
    public function actionStep3Recapitulation()
    {
        $this->checkShoppingCart();
        $this->checkShoppingCartProducts();
        $this->checkDelivery();
        $this->checkPayment();
        $this->checkBirthdayCoupon();
        $this->validateShoppingCart($this->shoppingCart);
    }

    public function renderStep3Recapitulation()
    {
        $this->template->activeStep = 4;
    }

    /**
     * @param $order string
     * @return void
     */
    public function actionStep4()
    {
        $noHeureka = (bool)$this->getHttpRequest()->getCookie('noheureka');

        $this->checkShoppingCart();
        $this->checkShoppingCartProducts();
        $this->checkDelivery();
        $this->checkPayment();
        $this->validateShoppingCart($this->shoppingCart);

        try {
            //save data
            //create order
            $this->database->beginTransaction();
            $orderCreateFacade = $this->orderCreateFacadeFactory->create();
            $order = $orderCreateFacade->createFromShoppingCart($this->shoppingCart->getEntity()->getId(), $noHeureka);
            $this->database->commit();

            $data = DataFactory::create($this->shoppingCart, 4);
            $this->dataLayer->add($data);
        } catch (ShoppingCartSaveFacadeException $exception) {
            $this->database->rollBack();
            $this->flashMessage($exception->getMessage(), 'danger');
        } catch (OrderCreateFacadeException $exception) {
            $this->database->rollBack();
            $this->flashMessage($exception->getMessage(), 'danger');
        }


        try {
            $orderObject = $order;
            $orderProducts = $orderObject->getProducts();
            $productId = Entities::getProperty($orderProducts, 'productId');

            //get brand for order products for ee tracking code
            $lockFacade = $this->parameterLockFacadeFactory->create();
            $brands = $lockFacade->getByKeyAndMoreProductId(Lock::EE_TRACKING_BRAND, $productId);

            //get categories for products
            $categories = [];
            $categoryFindFacade = $this->categoryFindFacadeFactory->create();
            foreach ($orderProducts as $orderProduct) {
                $_productId = $orderProduct->getProductId();
                $_categories = $categoryFindFacade->findPublishedByProductId($_productId);
                $_categories ? $categories[$_productId] = end($_categories) : null;
            }

            try {
                $this->database->beginTransaction();
                $orderFacade = $this->orderFacadeFactory->create();
                $orderFacade->update($orderObject->getId(), true);
                $this->database->commit();
            } catch (ProductStorageFacadeException $exception) {
                $this->database->rollBack();
                $this->logger->addError($exception->getMessage());
            }

            //show only once after sent order, because in other case everyone can see summary price of set order
            $this->remarketingCode->setPageType(CodeDTO::PAGE_TYPE_PURCHASE);
            $this->remarketingCode->setData([
                CodeDTO::DATA_TOTALVALUE_KEY => number_format($orderObject->getProductSummaryPriceWithoutVat(), 2, '.', ''),
            ]);
        } catch (OrderNotFoundException $exception) {
            $this->remarketingCode = null; //not send data for remarketing
        }

        $this->template->brands = $brands ?? null;
        $this->template->categories = $categories ?? null;
        $this->template->order = $orderObject ?? null;
        if (isset($orderObject) && $orderObject) {
            $this->ecomailHelper->sendOrder($orderObject);
        }

        //template parts
        $this->template->templateNavigation = true;
        $this->template->templateFooter = false;
    }


    /**
     * @return Benefit
     */
    public function createComponentBenefit(): Benefit
    {
        return $this->benefitFactory->create();
    }


    /**
     * @return ButtonNavigation
     */
    public function createComponentButtonNavigation(): ButtonNavigation
    {
        $nav = $this->buttonNavigationFactory->create();
        $nav->setCart($this->shoppingCart);
        return $nav;
    }


    /**
     * @return ContactInformationForm
     */
    public function createComponentContactInformationForm(): ContactInformationForm
    {
        $form = $this->contactInformationFormFactory->create();
        $form->setShoppingCart($this->shoppingCart);
        $this->loggedUser ? $form->setCustomer($this->loggedUser->getEntity()) : null;
        return $form;
    }


    /**
     * @return DeliveryForm
     */
    public function createComponentDeliveryForm(): DeliveryForm
    {
        $form = $this->deliveryFormFactory->create();
        $form->setShoppingCart($this->shoppingCart);
        return $form;
    }


    /**
     * @return OpportunityForm
     */
    public function createComponentOpportunityForm(): OpportunityForm
    {
        $data = $this->loggedUser ? Data::createFromCustomer($this->loggedUser->getEntity()) : Data::createFromShoppingCart($this->shoppingCart->getEntity());
        $data->setComment($this->translator->translate('form.opportunity.storeMeeting.shoppingCart.comment.value'));
        $form = $this->opportunityFormFactory->create();
        $form->setType(Opportunity::TYPE_ORDER_FINISH_ON_STORE);
        $form->setData($data);
        foreach ($this->shoppingCart->getProducts() as $product) {
            $catalogProductId = $product->getProductId();
            if ($catalogProductId) {
                $productDTO = $this->shoppingCart->getProductDTOByProductId($product->getProductId());
                $productDTO ? $form->addProduct(new Product($productDTO, $product->getQuantity())) : null;
            }
        }
        return $form;
    }


    /**
     * Check if exists shopping cart.
     * @return void
     * @throws AbortException
     */
    protected function checkShoppingCart()
    {
        if ($this->shoppingCart === null) {
            $this->redirect('ShoppingCart:step1');
        }
    }


    /**
     * Check if cart has any product
     * @throws AbortException
     */
    protected function checkShoppingCartProducts()
    {
        if (!$this->shoppingCart->getProducts()) {
            $this->redirect('ShoppingCart:step1');
        }
    }


    /**
     * Check if cart has set delivery.
     * @throws AbortException
     */
    protected function checkDelivery()
    {
        if (!$this->shoppingCart->getDelivery()) {
            $this->redirect('ShoppingCart:step2');
        }
    }


    /**
     * Check if cart has set payment.
     * @throws AbortException
     */
    protected function checkPayment()
    {
        if (!$this->shoppingCart->getPayment()) {
            $this->redirect('ShoppingCart:step2');
        }
    }

		
    /**
     * Check if birthday discount is valid a remove it if invalid.
		 * and inverse variant
     * @throws AbortException
     */
    protected function checkBirthdayCoupon()
    {
				if ($this->shoppingCart && $this->shoppingCart->getEntity()->getBirthdayCoupon()) {
						if (!$this->loggedUser || !$this->loggedUser->getEntity()->hasBirthdayCoupon()) {
								$this->database->beginTransaction();
								$this->shoppingCartSaveFacade->removeBirthdayCoupon((int)$this->shoppingCart->getEntity()->getId());
								$this->database->commit();								
								$this->loadShoppingCart(true);
						}
				}
				if ($this->shoppingCart && !$this->shoppingCart->getEntity()->getBirthdayCoupon()) {
						if ($this->loggedUser && $this->loggedUser->getEntity()->hasBirthdayCoupon()) {
								$this->database->beginTransaction();
								$this->shoppingCartSaveFacade->applyBirthdayCoupon((int)$this->shoppingCart->getEntity()->getId());
								$this->database->commit();								
								$this->loadShoppingCart(true);
						}
				}
				
    }

		
		

    /**
     * @return void
     * @throws AbortException
     */
    public function handleGoToDelivery()
    {
        if ($this->shoppingCart) {
            $data = DataFactory::create($this->shoppingCart, 2);
            $this->gtmDataLayer->add($data);

            $this->redirect('ShoppingCart:step2');
        }
    }


    /**
     * Validate saved data in shopping cart.
     * @param $cart ShoppingCartDTO
     * @return void
     * @throws AbortException
     */
    protected function validateShoppingCart(ShoppingCartDTO $cart)
    {
        try {
            $validityFacade = $this->validityFacadeFactory->create();
            $this->database->beginTransaction();
            $messages = $validityFacade->validity($cart->getEntity()->getId());
            $this->database->commit();

            foreach ($messages as $message) {
                $this->flashMessage($message->getMessage(), $message->getFlashMessageType());
            }
            $messages ? $this->redirect('ShoppingCart:step1') : null;
        } catch (ValidityFacadeException $exception) {
            $this->database->rollBack();
            $this->redirect('ShoppingCart:step1');
        }
    }
}