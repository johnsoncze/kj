<?php

declare(strict_types = 1);

namespace App\Opportunity\Product;

use App\Customer\CustomerNotFoundException;
use App\Customer\CustomerRepository;
use App\NotFoundException;
use App\Opportunity\OpportunityNotFoundException;
use App\Opportunity\OpportunityRepository;
use App\Opportunity\Product\Parameter\ParameterFactory;
use App\Product\Diamond\DiamondCollection;
use App\Product\Diamond\DiamondRepository;
use App\Product\ProductDTOFactory;
use App\Product\Production\ProductionTrait;
use App\Product\ProductNotFoundException;
use App\Product\ProductPublishedRepository AS CatalogProductPublishedRepository;
use App\Product\WeedingRing\Calculator\Calculator;
use App\Product\WeedingRing\Gender\Gender;
use App\Product\WeedingRing\Size\SizeRepository;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockRepository;
use App\ProductParameterGroup\Lock\Parameter\ParameterRepository;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductStorageFacade
{


    /** @var CatalogProductPublishedRepository */
    private $catalogProductRepo;

    /** @var CustomerRepository */
    private $customerRepo;

    /** @var ProductFactory */
    private $opportunityProductFactory;

    /** @var ProductRepository */
    private $opportunityProductRepo;

    /** @var OpportunityRepository */
    private $opportunityRepo;

    /** @var LockRepository */
    private $parameterLockGroupRepo;

    /** @var ParameterRepository */
    private $parameterLockRepo;

    /** @var DiamondRepository */
    private $productDiamondRepo;

    /** @var ProductDTOFactory */
    private $productDTOFactory;

    /** @var ParameterFactory */
    private $parameterFactory;

    /** @var \App\Opportunity\Product\Parameter\ParameterRepository */
    private $parameterRepo;

    /** @var \App\Product\Ring\Size\SizeRepository */
    private $ringSizeRepo;

    /** @var ITranslator */
    private $translator;

    /** @var Calculator */
    private $weedingRingCalculator;

    /** @var SizeRepository */
    private $weedingRingSizeRepo;



    public function __construct(\App\Opportunity\Product\Parameter\ParameterRepository $productParameterRepository,
								\App\Product\Ring\Size\SizeRepository $ringSizeRepository,
                                Calculator $calculator,
                                CatalogProductPublishedRepository $catalogProductRepo,
                                CustomerRepository $customerRepository,
                                DiamondRepository $diamondRepository,
                                LockRepository $lockRepository,
                                ParameterRepository $parameterRepository,
                                ProductFactory $productFactory,
                                ProductRepository $productRepo,
                                OpportunityRepository $opportunityRepository,
								ParameterFactory $parameterFactory,
								ProductDTOFactory $productDTOFactory,
                                ITranslator $translator,
                                SizeRepository $sizeRepository)
    {
        $this->catalogProductRepo = $catalogProductRepo;
        $this->customerRepo = $customerRepository;
        $this->parameterLockGroupRepo = $lockRepository;
        $this->parameterLockRepo = $parameterRepository;
        $this->opportunityProductFactory = $productFactory;
        $this->opportunityProductRepo = $productRepo;
        $this->opportunityRepo = $opportunityRepository;
        $this->productDiamondRepo = $diamondRepository;
        $this->parameterFactory = $parameterFactory;
        $this->parameterRepo = $productParameterRepository;
        $this->productDTOFactory = $productDTOFactory;
        $this->ringSizeRepo = $ringSizeRepository;
        $this->translator = $translator;
        $this->weedingRingCalculator = $calculator;
        $this->weedingRingSizeRepo = $sizeRepository;
    }



    /**
     * Add product to opportunity.
     * @param $opportunityId int
     * @param $productId int
	 * @param $quantity int
     * @return Product
     * @throws ProductStorageFacadeException
     */
    public function add(int $opportunityId, int $productId, int $quantity) : Product
    {
        try {
            $opportunity = $this->opportunityRepo->getOneById($opportunityId);
            $catalogProduct = $this->catalogProductRepo->getOneById($productId, $this->translator);
            $customer = $opportunity->getCustomerId() ? $this->customerRepo->getOneAllowedById((int)$opportunity->getCustomerId()) : NULL;
            $product = $this->opportunityProductFactory->createFromCatalogProduct($opportunity, $catalogProduct, $customer, $quantity);
            $this->opportunityProductRepo->save($product);

            //todo optimization - at this moment loads unnecessary objects
            //save product parameters
			$productParameters = [];
			$productsDTO = $this->productDTOFactory->createFromProducts([$catalogProduct]);
			$productDTO = end($productsDTO);
			$parameters = $productDTO->getVisibleParameters();
			foreach ($parameters as $parameter) {
				$productParameters[] = $this->parameterFactory->create($product, $parameter->getGroup(), $parameter);
			}
			$productParameters ? $this->parameterRepo->save($productParameters) : NULL;

            return $product;
        } catch (ProductNotFoundException $exception) {
            throw new ProductStorageFacadeException($this->translator->translate('product.not.found.general'));
        } catch (CustomerNotFoundException $exception) {
            throw new ProductStorageFacadeException($this->translator->translate('product.not.found.general'));
        }
    }



    /**
     * @param $opportunityId int
     * @param $productId int
     * @param $sizeId int
     * @param $gender string
     * @param $productionTime string
     * @param $diamondQualityId int|null
     * @return Product
     * @throws ProductStorageFacadeException
     * todo test
     */
    public function addWeedingRing(int $opportunityId,
                                   int $productId,
                                   int $sizeId,
                                   string $gender,
                                   string $productionTime,
                                   int $diamondQualityId = NULL)
    {
        try {
            $opportunity = $this->opportunityRepo->getOneById($opportunityId);
            $customerId = $opportunity->getCustomerId();
            $customer = $customerId ? $this->customerRepo->getOneAllowedById((int)$customerId) : NULL;
            $product = $this->catalogProductRepo->getOneById($productId, $this->translator);
            $size = $this->weedingRingSizeRepo->getOneByProductIdAndSizeIdAndGender($product->getId(), $sizeId, $gender);
            $ringSize = $this->ringSizeRepo->getOneById((int)$size->getSizeId());
            $weedingRing = clone $product;
            $weedingRing->setPrice($size->getPrice());
            $weedingRing->setVat($size->getVat());
            $diamonds = $this->productDiamondRepo->findByProductIdAndGender($product->getId(), $gender);
            $diamondCollection = $diamonds ? new DiamondCollection($diamonds) : NULL;
            $productionTimeObject = ProductionTrait::getProductionTimes()[$productionTime];
            $calculation = $this->weedingRingCalculator->calculate($customer, $weedingRing, $productionTimeObject, $diamondCollection, $diamondCollection ? $diamondQualityId : NULL);
            if ($diamondCollection !== NULL) {
                $lockGroup = $this->parameterLockGroupRepo->getOneByKey(Lock::DIAMOND_CALCULATOR);
                $qualityDiamond = $this->parameterLockRepo->findOneByLockIdAndParameterId($lockGroup->getId(), $diamondQualityId);
            }
            $opportunityProduct = $this->opportunityProductFactory->createFromWeedingRing($opportunity, $weedingRing, $calculation, new Gender($gender), $ringSize, $productionTimeObject, $customer, $qualityDiamond ?? NULL);
            $this->opportunityProductRepo->save($opportunityProduct);
            return $opportunityProduct;
        } catch (OpportunityNotFoundException $exception) {
            throw new ProductStorageFacadeException($exception->getMessage());
        } catch (ProductNotFoundException $exception) {
            throw new ProductStorageFacadeException($exception->getMessage());
        } catch (NotFoundException $exception) {
            throw new ProductStorageFacadeException($exception->getMessage());
        } catch (CustomerNotFoundException $exception) {
            throw new ProductStorageFacadeException($exception->getMessage());
        }
    }
}