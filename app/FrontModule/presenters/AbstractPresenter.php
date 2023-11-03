<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Components\SignInForm\SignInForm;
use App\Components\SignInForm\SignInFormFactory;
use App\Customer\Customer;
use App\Customer\CustomerSignFacadeException;
use App\Customer\CustomerSignFacadeFactory;
use App\Environment\Environment;
use App\Extensions\Nette\UserIdentity;
use App\FrontModule\Components\Breadcrumb\Breadcrumb;
use App\FrontModule\Components\Breadcrumb\BreadcrumbFactory;
use App\FrontModule\Components\Breadcrumb\Navigation;
use App\FrontModule\Components\Contact\Contact;
use App\FrontModule\Components\Contact\ContactFactory;
use App\FrontModule\Components\Menu\Header\Header;
use App\FrontModule\Components\Menu\Header\HeaderFactory;
use App\FrontModule\Components\OpportunityForm\Data;
use App\FrontModule\Components\OpportunityForm\OpportunityForm;
use App\FrontModule\Components\OpportunityForm\OpportunityFormFactory;
use App\FrontModule\Components\Page\Menu\Menu;
use App\FrontModule\Components\Page\Menu\MenuFactory;
use App\FrontModule\Components\Registration\Form\RegistrationForm;
use App\FrontModule\Components\Registration\Form\RegistrationFormFactory;
use App\FrontModule\Components\Search\Form\SearchForm;
use App\FrontModule\Components\Search\Form\SearchFormFactory;
use App\FrontModule\Components\ShoppingCart\Overview\Overview;
use App\FrontModule\Components\ShoppingCart\Overview\OverviewFactory;
use App\FrontModule\Components\Favourite\TopOverview\TopOverview;
use App\FrontModule\Components\Favourite\TopOverview\TopOverviewFactory;
use App\FrontModule\Components\Favourite\ProductHeart\ProductHeart;
use App\FrontModule\Components\Favourite\ProductHeart\ProductHeartFactory;
use App\FrontModule\Components\Store\ContactModal\ContactModal;
use App\FrontModule\Components\Store\ContactModal\ContactModalFactory;
use App\FrontModule\Components\Store\OpeningHours\OpeningHours;
use App\FrontModule\Components\Store\OpeningHours\OpeningHoursFactory;
use App\Google\TagManager\DataLayer;
use App\Google\TagManager\EnhancedEcommerce\DataFactory;
use App\Opportunity\Opportunity;
use App\Remarketing\Code\CodeDTO;
use App\ShoppingCart\ShoppingCartDTO;
use App\ShoppingCart\ShoppingCartFacadeException;
use App\ShoppingCart\ShoppingCartFacadeFactory;
use App\ShoppingCart\ShoppingCartSaveFacade;
use App\ShoppingCart\ShoppingCartSaveFacadeException;
use Kdyby\Monolog\Logger;
use Kdyby\Translation\ITranslator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Nette\InvalidArgumentException;
use Nette\Security\AuthenticationException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AbstractPresenter extends AbstractLanguagePresenter
{


    /** @var string */
    const BACKLINK = '_b';

    /** @var string */
    const USER_IDENTITY_NAMESPACE = 'frontend';

    /** @var string */
    const COOKIE_AGREEMENT = 'cookie_agreement';

    /** @var string */
    const POPUP_SHOWN = 'popup_shown';

    /** @var BreadcrumbFactory @inject */
    public $breadcrumbFactory;

    /** @var Navigation */
    public $breadcrumb;

    /** @var ContactFactory @inject */
    public $contactFactory;

    /** @var ContactModalFactory @inject */
    public $contactModalFactory;

    /** @var CustomerSignFacadeFactory @inject */
    public $customerSignFacadeFactory;

    /** @var Context @inject */
    public $database;

    /** @var Environment|null */
    protected $environment;

    /** @var DataLayer @inject */
    public $gtmDataLayer;

    /** @var HeaderFactory @inject */
    public $headerMenuFactory;

    /** @var Logger @inject */
    public $logger;

    /** @var \App\FrontModule\Components\NewsletterSubscriptionForm\FormFactory @inject */
    public $newsletterSubscriptionFormFactory;

    /** @var OpeningHoursFactory @inject */
    public $openingHoursFactory;

    /** @var OpportunityFormFactory @inject */
    public $opportunityFormFactory;

    /** @var MenuFactory @inject */
    public $pageMenuFactory;

    /** @var RegistrationFormFactory @inject */
    public $registrationFormFactory;

    /** @var CodeDTO|null */
    protected $remarketingCode;

    /** @var SearchFormFactory @inject */
    public $searchFormFactory;

    /** @var SignInFormFactory @inject */
    public $signInFormFactory;

    /** @var ShoppingCartFacadeFactory @inject */
    public $shoppingCartFacade;

    /** @var ShoppingCartSaveFacade @inject */
    public $shoppingCartSaveFacade;

    /** @var OverviewFactory @inject */
    public $shoppingCartOverviewFactory;

    /** @var TopOverviewFactory @inject */
    public $FavouriteTopOverviewFactory;
		
    /** @var ProductHeartFactory @inject */
    public $FavouriteProductHeartFactory;		
		
    /** @var ITranslator @inject */
    public $translator;

    /** @var UserIdentity|null */
    public $loggedUser;

    /** @var ShoppingCartDTO|null */
    public $shoppingCart;

    /**
     * @var int
     */
    public int $loggedCustomerDiscountRate;

    /**
     * @var int
     */
    public int $loggedCustomerBirthdayDiscountRate = 0;
		
    public bool $holidayCustomerDiscountRateActive;


    /**
     * @inheritdoc
     */
    public function startup()
    {
        parent::startup();
        $this->loggedUser = $this->getLoggedUser();

				$this->translator->setLocale('cs');
        $this->loggedCustomerDiscountRate = Customer::DISCOUNT;
				if ($this->loggedUser && $this->loggedUser->getEntity()->hasBirthdayCoupon()) {
		        $this->loggedCustomerBirthdayDiscountRate = Customer::BIRTHDAY_DISCOUNT;
				}
        $this->holidayCustomerDiscountRateActive = $this->context->getParameters()['marketingEvents']['holidayCustomerDiscountRateActive'];

        $this->environment = Environment::create();

        $this->loadShoppingCart();

        $this->breadcrumb = new Navigation();

        $this->template->loggedUser = $this->loggedUser;
        $this->template->loggedCustomerDiscountRate = $this->loggedCustomerDiscountRate;
        $this->template->loggedCustomerBirthdayDiscountRate = $this->loggedCustomerBirthdayDiscountRate;
        $this->template->holidayCustomerDiscountRateActive = $this->holidayCustomerDiscountRateActive;
        $this->template->shoppingCart = $this->shoppingCart;
        $this->template->environment = $this->environment;
        $this->template->showMeasuringCodes = $this->environment->showMeasuringCodes($this);
        $this->template->parameters = $this->context->getParameters();

        //seo
        $this->template->index = TRUE;
        $this->template->follow = TRUE;

        //template parts
        $this->template->templateNavigation = TRUE;
        $this->template->templateFooter = TRUE;

        //google tag manager
        $this->remarketingCode = new CodeDTO();
        $this->template->gtmDataLayer = $this->gtmDataLayer;
        $this->template->gtmData = $this->gtmDataLayer->getData();

        $this->template->recaptchaSiteKey = $this->context->getParameters()['recaptcha']['siteKey'] ?? NULL;

        //assets
        $this->template->mainJsVersion = filemtime(__DIR__ . '/../../../www/assets/front/js/main.min.js');
        $this->template->mainCssVersion = filemtime(__DIR__ . '/../../../www/assets/front/css/main.min.css');
        $ecomailCart = [];
        $ga4Cart = [];

        if ($this->shoppingCart) {
            foreach ($this->shoppingCart->getProducts() as $cartProduct) {
                $ecomailCart[] = [
                    'productId' => $cartProduct->getCatalogProduct()->getCode(),
                    'img_url' => 'https://jk.cz/upload/products/' . $cartProduct->getCatalogProduct()->getId() . '/' . $cartProduct->getCatalogProduct()->getPhoto(),
                    'url' => $cartProduct->getCatalogProduct()->getTranslation()->getUrl(),
                    'name' => $cartProduct->getCatalogProduct()->getTranslation()->getName(),
                    'price' => $cartProduct->getCatalogProduct()->getPrice(),
                    'descripiton' => $cartProduct->getCatalogProduct()->getTranslation()->getDescription(),
                    //                    'amount' => $cartProduct->getQuantity(),
                ];
                $ga4Cart[] = [
                    'item_id' => $cartProduct->getCatalogProduct()->getCode(),
                    'item_name' => $cartProduct->getCatalogProduct()->getTranslation()->getName(),
                    'price' => $cartProduct->getCatalogProduct()->getPrice(),
                    'quantity' => $cartProduct->getQuantity(),
                ];
            }
        }
        $this->template->ecomailCart = json_encode($ecomailCart);
        $this->template->ga4Cart = $ga4Cart;

    }

    /**
     * @inheritdoc
     */
    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->cookieAgreement = $this->getHttpRequest()->getCookie(self::COOKIE_AGREEMENT);
        $this->template->popupShown = $this->getHttpRequest()->getCookie(self::POPUP_SHOWN);
        $this->template->remarketingCode = $this->remarketingCode;
        $this->template->cacheLogged = $this->getUser()->isLoggedIn() ? 'logged' : 'anonym';

        $this->template->cookieBarHidden = $this->getHttpRequest()->getCookie('cookieBarHidden') == "1";
        $this->template->cookies_analytics = $this->getHttpRequest()->getCookie('cookies_analytics') == "1";
        $this->template->cookies_marketing = $this->getHttpRequest()->getCookie('cookies_marketing') == "1";

        if ($this->template->cookies_analytics && $this->template->cookies_marketing) {
            $this->template->googleTagManagerId = $this->context->getParameters()['googleTagManager']['id'] ?? null;
        } else {
            $this->template->googleTagManagerId = $this->context->getParameters()['googleTagManager']['idNoCookies'] ?? null;
        }

        $this->template->ogPrefix = $this->context->getParameters()['ogPrefix'] ?? null;
        $this->template->ogUrl = $this->getHttpRequest()->getUrl()->getPath();
        if (!isset($this->template->ogImage)) {
            $this->template->ogImage = '/assets/front/images/jk_og.jpg';
        }

    }



    /**
	 * @return ContactModal
    */
	public function createComponentContactModal() : ContactModal
	{
		return $this->contactModalFactory->create();
	}



    /**
     * @return RegistrationForm
     */
    public function createComponentRegistrationForm() : RegistrationForm
    {
        return $this->registrationFormFactory->create();
    }



    /**
     * @return SignInForm
     * @throws AuthenticationException
     * @throws AbortException
     */
    public function createComponentSignInForm() : SignInForm
    {
        $form = $this->signInFormFactory->create();
        $form->onSuccess(function (Form $form) {
            try {
                $values = $form->getValues();
                $signFacade = $this->customerSignFacadeFactory->create();
                $identity = $signFacade->identify($values->email, $values->password);
                $user = $this->getUser();
                $user->getStorage()->setNamespace(self::USER_IDENTITY_NAMESPACE);
                $user->login($identity);

                try {
                    if ($this->shoppingCart) {
                        $this->database->beginTransaction();
                        $returnState = $this->shoppingCartSaveFacade->setCustomerOnCart($this->shoppingCart->getEntity()->getId(), $user->getId());
                        $this->database->commit();
                    }
                } catch (ShoppingCartSaveFacadeException $exception) {
                    $this->database->rollBack();
                    $this->logger->addError($exception->getMessage());
                }

                $this->flashMessage($this->translator->translate('form.sign.in.message.success'), 'success');
                if (isset($returnState) && $returnState === ShoppingCartSaveFacade::MERGED_PRODUCTS) {
                    $this->flashMessage($this->translator->translate('shopping-cart.merge.product'), 'info');
                }

                //log
                $this->logger->addInfo(sprintf('sign.in.form: Přihlášení zákazníka s id \'%d\'.', $user->getId()), ['environment' => $this->environment->getType(), 'route' => $this->getAction(TRUE)]);

                $this->restoreRequest($this->getParameter(self::BACKLINK));
                $this->redirect('Account:default');
            } catch (CustomerSignFacadeException $exception) {
                $this->flashMessage($exception->getMessage(), 'danger');
            }
        });
        return $form;
    }



    /**
     * @return \App\FrontModule\Components\NewsletterSubscriptionForm\Form
     */
    public function createComponentNewsletterSubscriptionForm() : \App\FrontModule\Components\NewsletterSubscriptionForm\Form
    {
        $form = $this->newsletterSubscriptionFormFactory->create();
        if ($this->loggedUser !== NULL) { //set default email of logged user
            $form->setCustomer($this->loggedUser->getEntity());
        }
        return $form;
    }



    /**
     * @return Overview
     */
    public function createComponentShoppingCartOverview() : Overview
    {
        $overview = $this->shoppingCartOverviewFactory->create();
        $this->shoppingCart ? $overview->setShoppingCart($this->shoppingCart) : NULL;
        return $overview;
    }


		
    /**
     * @return Overview
     */
    public function createComponentFavouriteTopOverview() : TopOverview
    {
        $overview = $this->FavouriteTopOverviewFactory->create();
        return $overview;
    }
		
		
    /**
     * @return ProductHeart
     */
    public function createComponentFavouriteProductHeart() : ProductHeart
    {
        $productHeart = $this->FavouriteProductHeartFactory->create();
        return $productHeart;
    }
				
		
		

    /**
     * Get logged user if exists.
     * @return UserIdentity|null
     */
    private function getLoggedUser()
    {
        $user = $this->getUser();
        $user->getStorage()->setNamespace(self::USER_IDENTITY_NAMESPACE);
        return $this->loggedUser = $user->getIdentity();
    }



    /**
     * @param $reLoad bool
     * @return ShoppingCartDTO|null
     * @throws ShoppingCartFacadeException
     * @throws InvalidArgumentException
     */
    public function loadShoppingCart(bool $reLoad = FALSE)
    {
        static $cartFacade;

        if (!$this->shoppingCart || $reLoad === TRUE) {
            if ($cart = $this->shoppingCartSaveFacade->find($this->loggedUser ? $this->loggedUser->getEntity()->getId() : NULL)) {
                if ($cartFacade === NULL) {
                    $cartFacade = $this->shoppingCartFacade->create();
                }
                $this->shoppingCart = $cartFacade->getDTO($cart->getId());
            }
        }
        return $this->shoppingCart;
    }



    /**
	 * @return Breadcrumb
    */
    public function createComponentBreadcrumb() : Breadcrumb
	{
		$breadcrumb = $this->breadcrumbFactory->create();
		$breadcrumb->setNavigation($this->breadcrumb);
		return $breadcrumb;
	}



    /**
     * @return Contact
     */
    public function createComponentContact() : Contact
    {
        return $this->contactFactory->create();
    }




    /**
     * @return OpportunityForm
     */
    public function createComponentContactForm() : OpportunityForm
    {
        $parameters = $this->getRequest()->getParameters();
        unset($parameters['action']); //remove presenter action from parameters

        $form = $this->opportunityFormFactory->create();
        $form->setType(Opportunity::TYPE_CONTACT_FORM);
        $this->loggedUser ? $form->setData(Data::createFromCustomer($this->loggedUser->getEntity())) : NULL;
        $parameters ? $form->setPageArguments($parameters) : NULL;
        return $form;
    }



	/**
	 * @return OpportunityForm
	 */
	public function createComponentStoreMeetingForm() : OpportunityForm
	{
		$parameters = $this->getRequest()->getParameters();
		unset($parameters['action']); //remove presenter action from parameters

		$form = $this->opportunityFormFactory->create();
		$form->setType(Opportunity::TYPE_STORE_MEETING);
		$this->loggedUser ? $form->setData(Data::createFromCustomer($this->loggedUser->getEntity())) : NULL;
		$parameters ? $form->setPageArguments($parameters) : NULL;
		return $form;
	}



    /**
     * @return Header
     */
    public function createComponentHeaderMenu() : Header
    {
        $menu = $this->headerMenuFactory->create();
        $menu->setLanguage($this->language);
        return $menu;
    }



    /**
     * @return OpeningHours
     */
    public function createComponentOpeningHours() : OpeningHours
    {
        return $this->openingHoursFactory->create();
    }



    /**
     * @return Menu
     */
    public function createComponentPageMenu() : Menu
    {
        $menu = $this->pageMenuFactory->create();
        $menu->setLanguage($this->languageEntity);
        return $menu;
    }



    /**
     * @return SearchForm
     */
    public function createComponentSearchForm() : SearchForm
    {
        return $this->searchFormFactory->create();
    }



    /**
	 * @return void
	 * @throws AbortException
    */
    public function handleGoToShoppingCart()
	{
		if ($this->shoppingCart) {
			$data = DataFactory::create($this->shoppingCart);
			$this->gtmDataLayer->add($data);
		}

		$this->redirect('ShoppingCart:step1');
	}
}
