<?php

declare(strict_types = 1);

namespace App\Product\WeedingRing\Calculator;

use App\Customer\CustomerNotFoundException;
use App\Customer\CustomerRepository;
use App\Helpers\Entities;
use App\NotFoundException;
use App\Product\Diamond\DiamondCollection;
use App\Product\Diamond\DiamondRepository;
use App\Product\Production\ProductionTrait;
use App\Product\ProductNotFoundException;
use App\Product\ProductPublishedRepository;
use App\Product\WeedingRing\Gender\Gender;
use App\Product\WeedingRing\Size\SizeRepository;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CalculatorFacade
{


    /** @var Calculator */
    private $calculator;

    /** @var CustomerRepository */
    private $customerRepo;

    /** @var DiamondRepository */
    private $productDiamondRepo;

    /** @var ProductPublishedRepository */
    private $productRepo;

    /** @var SizeRepository */
    private $sizeRepo;

    /** @var ITranslator */
    private $translator;



    public function __construct(Calculator $calculator,
                                CustomerRepository $customerRepository,
                                DiamondRepository $diamondRepository,
                                ITranslator $translator,
                                ProductPublishedRepository $productRepository,
                                SizeRepository $sizeRepository)
    {
        $this->calculator = $calculator;
        $this->customerRepo = $customerRepository;
        $this->productDiamondRepo = $diamondRepository;
        $this->productRepo = $productRepository;
        $this->sizeRepo = $sizeRepository;
        $this->translator = $translator;
    }



    /**
     * @param $productId int
     * @param $maleSizeId int
     * @param $femaleSizeId int
     * @param $productionTime string
     * @param $customerId int|null
     * @param $diamondQualityId int|null
     * @return Calculation
     * @throws CalculatorFacadeException
     * todo test
     */
    public function calculate(int $productId,
                              int $maleSizeId,
                              int $femaleSizeId,
                              string $productionTime,
                              int $customerId = NULL,
                              int $diamondQualityId = NULL) : Calculation
    {
        try {
            $customer = $customerId !== NULL ? $this->customerRepo->getOneAllowedById($customerId) : NULL;
            $productionTimeObject = ProductionTrait::getProductionTimes()[$productionTime]; //todo načítat z databáze!
            $product = $this->productRepo->getOneById($productId, $this->translator);
            $productDiamonds = $this->productDiamondRepo->findByProductId($product->getId());
            $productDiamonds = $productDiamonds ? Entities::toSegment($productDiamonds, 'gender') : [];

            //male ring
            $maleSize = $this->sizeRepo->getOneByProductIdAndSizeIdAndGender($product->getId(), $maleSizeId, Gender::MALE);
            $maleProduct = clone $product; //workaround
            $maleProduct->setPrice($maleSize->getPrice());
            $maleProduct->setVat($maleSize->getVat());
            $maleDiamonds = isset($productDiamonds[Gender::MALE]) ? new DiamondCollection($productDiamonds[Gender::MALE]) : NULL;
            $malePrice = $this->calculator->calculate($customer, $maleProduct, $productionTimeObject, $maleDiamonds, $maleDiamonds ? $diamondQualityId : NULL);

            //female ring
            $femaleSize = $this->sizeRepo->getOneByProductIdAndSizeIdAndGender($product->getId(), $femaleSizeId, Gender::FEMALE);
            $femaleProduct = clone $product; //workaround
            $femaleProduct->setPrice($femaleSize->getPrice());
            $femaleProduct->setVat($femaleSize->getVat());
            $femaleDiamonds = isset($productDiamonds[Gender::FEMALE]) ? new DiamondCollection($productDiamonds[Gender::FEMALE]) : NULL;
            $femalePrice = $this->calculator->calculate($customer, $femaleProduct, $productionTimeObject, $femaleDiamonds, $femaleDiamonds ? $diamondQualityId : NULL);

            return new Calculation($malePrice, $femalePrice);
        } catch (CalculatorFacadeException $exception) {
            throw new CalculatorFacadeException($exception->getMessage());
        } catch (CustomerNotFoundException $exception) {
            throw new CalculatorFacadeException($exception->getMessage());
        } catch (ProductNotFoundException $exception) {
            throw new CalculatorFacadeException($exception->getMessage());
        } catch (NotFoundException $exception) {
            throw new CalculatorFacadeException($exception->getMessage());
        }
    }
}