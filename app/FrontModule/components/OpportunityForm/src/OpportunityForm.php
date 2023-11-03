<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\OpportunityForm;

use App\FrontModule\Components\FormSpamProtection;
use App\FrontModule\Components\Store\OpeningHours\OpeningHours;
use App\FrontModule\Components\Store\OpeningHours\OpeningHoursFactory;
use App\Google\TagManager\DataLayer;
use App\Opportunity\Opportunity;
use App\Opportunity\OpportunityStorageFacadeException;
use App\Opportunity\OpportunityStorageFacadeFactory;
use App\Opportunity\Product\ProductStorageFacadeException;
use App\Opportunity\Product\ProductStorageFacadeFactory;
use App\Opportunity\SendEmailFacadeFactory;
use Kdyby\Translation\ITranslator;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OpportunityForm extends Control
{


    use FormSpamProtection;

    /** @var string */
    const CONTACT_FORM_ID = 'contact-form';
    const PRODUCT_DEMAND_POPUP_ID = 'product-demand';
    const PRODUCT_STORE_MEETING_POPUP_ID = 'product-meeting';


    /** @var Data|null */
    private $data;

    /** @var Context */
    private $database;

    /** @var DataLayer */
    private $gtmDataLayer;

    /** @var Product[]|null */
    private $products = [];

    /** @var OpeningHoursFactory */
    private $openingHoursFactory;

    /** @var OpportunityStorageFacadeFactory */
    private $opportunityFacadeFactory;

    /** @var OpportunityFormContainerFactory */
    private $opportunityFormContainerFactory;

    /** @var ProductStorageFacadeFactory */
    private $opportunityProductStorageFacadeFactory;

    /** @var SendEmailFacadeFactory */
    private $opportunitySendEmailFacadeFactory;

    /** @var array arguments of url */
    private $pageArguments = [];

    /** @var ITranslator */
    private $translator;

    /** @var string */
    private $type = Opportunity::TYPE_PRODUCT_DEMAND;



    public function __construct(Context $database,
                                DataLayer $dataLayer,
                                OpeningHoursFactory $openingHoursFactory,
                                OpportunityFormContainerFactory $opportunityFormContainerFactory,
                                OpportunityStorageFacadeFactory $opportunityFacadeFactory,
                                ProductStorageFacadeFactory $opportunityProductStorageFacadeFactory,
                                SendEmailFacadeFactory $sendEmailFacadeFactory,
                                ITranslator $translator)
    {
        parent::__construct();
        $this->database = $database;
        $this->gtmDataLayer = $dataLayer;
        $this->openingHoursFactory = $openingHoursFactory;
        $this->opportunityFacadeFactory = $opportunityFacadeFactory;
        $this->opportunityFormContainerFactory = $opportunityFormContainerFactory;
        $this->opportunityProductStorageFacadeFactory = $opportunityProductStorageFacadeFactory;
        $this->opportunitySendEmailFacadeFactory = $sendEmailFacadeFactory;
        $this->translator = $translator;
    }



    /**
     * @param $data Data
     * @return self
     */
    public function setData(Data $data) : self
    {
        $this->data = $data;
        return $this;
    }



    /**
     * @param $product Product
     * @return self
     */
    public function addProduct(Product $product) : self
    {
        $this->products[] = $product;
        return $this;
    }



    /**
     * @param $args array
     * @return self
     */
    public function setPageArguments(array $args) : self
    {
        $this->pageArguments = $args;
        return $this;
    }



    /**
     * @param $type string
     * @return self
     */
    public function setType(string $type) : self
    {
        $this->type = $type;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $opportunityFormContainer = $this->opportunityFormContainerFactory->create();
        $this->hasCustomer() && $opportunityFormContainer->applyCustomer();

        $form = new Form();
        $this->addSpamProtection($form);
        $form->addComponent($opportunityFormContainer, OpportunityFormContainer::NAME);
        $form->addSubmit('submit', $this->translator->translate('form.opportunity.input.submit.label'));
        $form->onSuccess[] = [$this, 'formSuccess'];

        if ($this->data) {
            $opportunityFormContainer->setDefaultsFromData($this->data);
        }

        return $form;
    }



    /**
     * @return OpeningHours
     */
    public function createComponentOpeningHours() : OpeningHours
    {
        return $this->openingHoursFactory->create();
    }



    /**
     * @param $form Form
     * @return void
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $opportunityFormValues = $values[OpportunityFormContainer::NAME];
        $presenter = $this->getPresenter();
        $customerId = $this->data ? $this->data->getCustomerId() : NULL;
        $page = $presenter->getAction(TRUE);
        $this->processSpamRequest($values, $presenter, $this->translator);

        try {
            $opportunityFacade = $this->opportunityFacadeFactory->create();
            $opportunityProductFacade = $this->opportunityProductStorageFacadeFactory->create();

            $this->database->beginTransaction();

            //add opportunity
            $opportunity = $opportunityFacade->add($customerId, $opportunityFormValues->firstName, $opportunityFormValues->lastName, $opportunityFormValues->preferredContact,
                $opportunityFormValues->email ?: NULL, $opportunityFormValues->telephone ?: NULL, $opportunityFormValues->requestDate ?: NULL, $opportunityFormValues->comment ?: NULL, $page, NULL, $this->type, $this->pageArguments);

            //add products to opportunity
            foreach ($this->products as $product) {
                $opportunityProductFacade->add($opportunity->getId(), $product->getProduct()->getProduct()->getId(), $product->getQuantity());
            }

            //send email
            $emailFacade = $this->opportunitySendEmailFacadeFactory->create();
            $emailFacade->sendConfirm($opportunity->getId());

            $this->database->commit();

            //add data for google tag manager
            $this->gtmDataLayer->add([
                'formular' => $opportunity->getGtmType(),
                'event' => 'formSent',
            ]);

            $presenter->flashMessage($this->translator->translate('form.opportunity.message.success'), 'success');
            $presenter->redirect('this');
        } catch (OpportunityStorageFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($this->translator->translate('form.opportunity.message.error'), 'danger');
        } catch (ProductStorageFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($this->translator->translate('form.opportunity.message.error'), 'danger');
        }
    }



    public function render()
    {
        $this->template->data = $this->data;
        $this->template->hasCustomer = $this->hasCustomer();
        $this->template->parameters = $this->getPresenter()->context->getParameters();
        $this->template->popUpId = self::PRODUCT_DEMAND_POPUP_ID;
        $this->template->products = $this->products;
        $this->template->title = $this->translator->translate('form.demand.title');

        $this->template->setFile(__DIR__ . '/Templates/productDemand.latte');
        $this->template->render();
    }



    /**
     * @deprecated only for BC
     */
    public function renderProductDemand()
    {
        $this->render();
    }



    public function renderProductStoreMeeting()
    {
        $this->template->data = $this->data;
        $this->template->hasCustomer = $this->hasCustomer();
        $this->template->parameters = $this->getPresenter()->context->getParameters();
        $this->template->popUpId = self::PRODUCT_STORE_MEETING_POPUP_ID;
        $this->template->products = $this->products;
        $this->template->title = $this->translator->translate('form.opportunity.storeMeeting.title');

        $this->template->setFile(__DIR__ . '/Templates/productDemand.latte');
        $this->template->render();
    }



    public function renderContactForm()
    {
        $this->template->hasCustomer = $this->hasCustomer();

        $this->template->setFile(__DIR__ . '/Templates/contactForm.latte');
        $this->template->render();
    }



    public function renderPopupContactForm()
    {
        $this->template->hasCustomer = $this->hasCustomer();
        $this->template->popUpId = self::CONTACT_FORM_ID;
        $this->template->title = $this->translator->translate('form.opportunity.contact.title');

        $this->template->setFile(__DIR__ . '/Templates/popupContactForm.latte');
        $this->template->render();
    }



    /**
     * @return bool
     */
    private function hasCustomer() : bool
    {
        return $this->data && (bool)$this->data->getCustomerId();
    }
}