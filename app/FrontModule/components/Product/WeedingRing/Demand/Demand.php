<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\WeedingRing\Demand;

use App\Customer\Customer;
use App\Diamond\Price\PriceRepository;
use App\FrontModule\Components\OpportunityForm\Data;
use App\FrontModule\Components\OpportunityForm\OpportunityFormContainer;
use App\FrontModule\Components\OpportunityForm\OpportunityFormContainerFactory;
use App\FrontModule\Components\Product\MetaSmallBlock\MetaSmallBlock;
use App\FrontModule\Components\Product\MetaSmallBlock\MetaSmallBlockFactory;
use App\FrontModule\Components\Product\ProductionTimeForm\FormContainer;
use App\FrontModule\Components\Product\ProductionTimeForm\FormContainerFactory;
use App\Google\TagManager\DataLayer;
use App\Helpers\Entities;
use App\Helpers\Prices;
use App\Opportunity\Opportunity;
use App\Opportunity\OpportunityStorageFacadeException;
use App\Opportunity\OpportunityStorageFacadeFactory;
use App\Opportunity\Product\ProductStorageFacadeException;
use App\Opportunity\Product\ProductStorageFacadeFactory;
use App\Opportunity\SendEmailFacadeFactory;
use App\Product\Diamond\DiamondFacadeFactory;
use App\Product\Product;
use App\Product\Ring\Size\Size;
use App\Product\Ring\Size\SizeRepository;
use App\Product\WeedingRing\Calculator\CalculatorFacadeException;
use App\Product\WeedingRing\Calculator\CalculatorFacadeFactory;
use App\Product\WeedingRing\Gender\Gender;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockFacadeFactory;
use Kdyby\Monolog\Logger;
use Kdyby\Translation\ITranslator;
use Nette\Application\AbortException;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Demand extends Control
{


    /** @var string parameter keys */
    const MALE_SIZE_ID = 'maleSizeId';
    const FEMALE_SIZE_ID = 'femaleSizeId';
    const PRODUCTION_TIME = 'productionTime';
    const DIAMOND_QUALITY_ID = 'diamondQualityId';


    /** @var string */
    const LOGGER_NAMESPACE = 'weedingRing.demand.calculate';

    /** @var CalculatorFacadeFactory */
    private $calculatorFacadeFactory;

    /** @var Customer|null */
    private $customer;

    /** @var Context */
    private $database;

    /** @var DiamondFacadeFactory */
    private $diamondFacadeFactory;

    /** @var array */
    private $diamondList = [];

    /** @var DataLayer */
    private $gtmDataLayer;

    /** @var Logger */
    private $logger;

    /** @var MetaSmallBlockFactory */
    private $metaSmallBlockFactory;

    /** @var OpportunityFormContainerFactory */
    private $opportunityFormContainer;

    /** @var ProductStorageFacadeFactory */
    private $opportunityProductStorageFacadeFactory;

    /** @var OpportunityStorageFacadeFactory */
    private $opportunityStorageFacadeFactory;

    /** @var LockFacadeFactory */
    private $parameterGroupLockFacadeFactory;

    /** @var Product|null */
    private $product;

    /** @var FormContainerFactory */
    private $productionTimeFormFactory;

    /** @var SendEmailFacadeFactory */
    private $sendEmailFacade;

    /** @var SizeRepository */
    private $sizeRepo;

    /** @var ITranslator */
    private $translator;



    public function __construct(CalculatorFacadeFactory $calculatorFacadeFactory,
                                Context $context,
								DataLayer $dataLayer,
                                DiamondFacadeFactory $diamondFacadeFactory,
                                FormContainerFactory $formContainerFactory,
                                ITranslator $translator,
                                LockFacadeFactory $lockFacadeFactory,
                                Logger $logger,
                                MetaSmallBlockFactory $metaSmallBlockFactory,
                                OpportunityFormContainerFactory $opportunityFormContainerFactory,
                                PriceRepository $priceRepository,
                                ProductStorageFacadeFactory $productStorageFacadeFactory,
                                OpportunityStorageFacadeFactory $opportunityStorageFacadeFactory,
                                SendEmailFacadeFactory $sendEmailFacadeFactory,
                                SizeRepository $sizeRepository)
    {
        parent::__construct();
        $this->calculatorFacadeFactory = $calculatorFacadeFactory;
        $this->database = $context;
        $this->gtmDataLayer = $dataLayer;
        $this->diamondFacadeFactory = $diamondFacadeFactory;
        $this->parameterGroupLockFacadeFactory = $lockFacadeFactory;
        $this->logger = $logger;
        $this->metaSmallBlockFactory = $metaSmallBlockFactory;
        $this->opportunityFormContainer = $opportunityFormContainerFactory;
        $this->opportunityProductStorageFacadeFactory = $productStorageFacadeFactory;
        $this->opportunityStorageFacadeFactory = $opportunityStorageFacadeFactory;
        $this->productionTimeFormFactory = $formContainerFactory;
        $this->sendEmailFacade = $sendEmailFacadeFactory;
        $this->sizeRepo = $sizeRepository;
        $this->translator = $translator;
    }



    /**
     * @param $customer Customer
     * @return self
     */
    public function setCustomer(Customer $customer) : self
    {
        $this->customer = $customer;
        return $this;
    }



    /**
     * @param $product Product
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setProduct(Product $product) : self
    {
        $type = Product::WEEDING_RING_PAIR_TYPE;
        if ($product->isWeedingRingPair() === FALSE) {
            throw new \InvalidArgumentException(sprintf('Invalid product. Product must be type of \'%s\'.', $type));
        }
        $this->product = $product;
        $this->diamondList = $this->getDiamondList();
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $sizeList = $this->getSizeList();
        $opportunityForm = $this->opportunityFormContainer->create();
        $this->customer && $opportunityForm->applyCustomer();
        $productionTimeForm = $this->productionTimeFormFactory->create();
        $productionTimeForm->getComponent('productionTime')->setAttribute('class', 'calculate js-selectfield select2-hidden-accessible');

        $form = new Form();
        $form->addSelect('maleSize', $this->translator->translate('product.weedingRingPair.demand.male.size.label'), $sizeList)
            ->setAttribute('class', 'calculate js-selectfield select2-hidden-accessible');
        $form->addSelect('femaleSize', $this->translator->translate('product.weedingRingPair.demand.female.size.label'), $sizeList)
            ->setAttribute('class', 'calculate js-selectfield select2-hidden-accessible');
        if ($this->diamondList) {
            $form->addSelect('diamond', $this->translator->translate('form.diamondQuality.label'), $this->diamondList)
                ->setDefaultValue(min(array_keys($this->diamondList)))
                ->setAttribute('class', 'calculate js-selectfield select2-hidden-accessible');
        }
        $form->addComponent($productionTimeForm, FormContainer::NAME);
        $form->addComponent($opportunityForm, OpportunityFormContainer::NAME);
        $form->addSubmit('submit', $this->translator->translate('form.demand.input.submit.label'))
            ->setAttribute('class', 'Button');
        $form->onSuccess[] = [$this, 'formSuccess'];

        if ($this->customer) {
            $opportunityForm->setDefaultsFromData(Data::createFromCustomer($this->customer));
        }

        return $form;
    }



    /**
     * @param $form Form
     * @return void
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $presenter = $this->getPresenter();
        $values = $form->getValues();
        $diamondQualityId = $values->diamond ?? NULL;
        $opportunityValues = $values[OpportunityFormContainer::NAME];
        $productionTime = $values[FormContainer::NAME]->productionTime;
        $pageArguments = ['url' => $presenter->getParameter('url'), 'lang' => $presenter->getParameter('lang')];

        try {
            $this->database->beginTransaction();

            //add opportunity
            $opportunityFacade = $this->opportunityStorageFacadeFactory->create();
            $opportunity = $opportunityFacade->add($this->customer ? $this->customer->getId() : NULL, $opportunityValues->firstName, $opportunityValues->lastName,
                $opportunityValues->preferredContact, $opportunityValues->email ?: NULL, $opportunityValues->telephone ?: NULL, $opportunityValues->comment ?: NULL,
                $presenter->getAction(TRUE), NULL, Opportunity::TYPE_WEEDING_RING_DEMAND, $pageArguments);

            //add products
            $opportunityProductFacade = $this->opportunityProductStorageFacadeFactory->create();
            $opportunityProductFacade->addWeedingRing($opportunity->getId(), $this->product->getId(), $values->maleSize, Gender::MALE, $productionTime, $diamondQualityId);
            $opportunityProductFacade->addWeedingRing($opportunity->getId(), $this->product->getId(), $values->femaleSize, Gender::FEMALE, $productionTime, $diamondQualityId);

            //send email
            $this->sendEmailFacade->create()->sendConfirm($opportunity->getId());

            //add google analytics event
			$this->gtmDataLayer->add([
				'formular' => $opportunity->getGtmType(),
				'event' => 'formSent',
			]);

            $this->database->commit();

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



    /**
     * @return void
     * @throws AbortException
     */
    public function handleCalculate()
    {
        $presenter = $this->getPresenter();
        if ($presenter->isAjax()) {
            $code = 0;
            $productId = $this->product->getId();
            $customerId = $this->customer ? $this->customer->getId() : NULL;

            //request parameters
            $maleSizeId = $this->getParameter(self::MALE_SIZE_ID);
            $femaleSizeId = $this->getParameter(self::FEMALE_SIZE_ID);
            $productionTime = $this->getParameter(self::PRODUCTION_TIME);
            $diamondQualityId = $this->getParameter(self::DIAMOND_QUALITY_ID);
            $logParams = ['productId' => $productId, 'customerId' => $customerId, 'maleSizeId' => $maleSizeId, 'femaleSizeId' => $femaleSizeId, 'diamondQuality' => $diamondQualityId, 'productionTime' => $productionTime];

            try {
                if ($maleSizeId === NULL || $femaleSizeId === NULL || $productionTime === NULL) {
                    $this->logger->addError(self::LOGGER_NAMESPACE . ': Missing some required parameter.', $logParams);
                    $code = 1;
                } else {
                    $calculatorFacade = $this->calculatorFacadeFactory->create();
                    $calculation = $calculatorFacade->calculate($productId, (int)$maleSizeId, (int)$femaleSizeId, $productionTime, $customerId, $diamondQualityId ? (int)$diamondQualityId : NULL);
                    $this->logger->addDebug(self::LOGGER_NAMESPACE . ': Calculation has been finished.', ['requestParams' => $logParams, 'malePrice' => print_r($calculation->getMalePrice(), TRUE), 'femalePrice' => print_r($calculation->getFemalePrice(), TRUE), 'summaryPrice' => print_r($calculation->getSummaryPrice(), TRUE)]);
                }
            } catch (CalculatorFacadeException $exception) {
                $this->logger->addError(sprintf(self::LOGGER_NAMESPACE . ': An error has been occurred on calculate. Error: %s', $exception->getMessage()), $logParams);
                $code = 1;
            }

            $summaryPriceBeforeDiscount = isset($calculation) ? Prices::toUserFriendlyFormat($calculation->getSummaryPrice()->summaryBeforeDiscount) : NULL;
            $summaryPrice = isset($calculation) ? Prices::toUserFriendlyFormat($calculation->getSummaryPrice()->summary) : NULL;
            $presenter->sendResponse(new JsonResponse(['code' => $code, 'summaryPriceBeforeDiscount' => $summaryPriceBeforeDiscount, 'summaryPrice' => $summaryPrice]));
        }
    }



    public function render()
    {
    	$lockFacade = $this->parameterGroupLockFacadeFactory->create();

        $this->template->customer = $this->customer;
        $this->template->diamondList = $this->diamondList;
        $this->template->diamondParameterGroup = $this->diamondList ? $lockFacade->getOneGroupByKey(Lock::WEEDING_RING_DEMAND_DIAMOND_QUALITY) : NULL;
        $this->template->product = $this->product;
        $this->template->ringSizeParameterGroup = $lockFacade->getOneGroupByKey(Lock::WEEDING_RING_DEMAND_SIZE_PARAMETER_GROUP);

        $this->template->setFile(__DIR__ . '/templates/default.latte');
        $this->template->render();
    }



    /**
     * @return MetaSmallBlock
    */
    public function createComponentMetaSmallBlock() : MetaSmallBlock
    {
        $meta = $this->metaSmallBlockFactory->create();
        $meta->setProduct($this->product);
        $this->customer ? $meta->setCustomer($this->customer) : NULL;
        return $meta;
    }



    /**
     * @return array
     */
    private function getDiamondList() : array
    {
        $diamondFacade = $this->diamondFacadeFactory->create();
        $parameters = $diamondFacade->findDiamondQualitiesByProductId($this->product->getId());
        return $parameters ? Entities::toPair($parameters, 'parameterId', 'value') : [];
    }



    /**
     * @return Size[]|array
     */
    private function getSizeList() : array
    {
        $sizes = $this->sizeRepo->findAll();
        return $sizes ? Entities::toPair($sizes, 'id', 'size') : [];
    }
}