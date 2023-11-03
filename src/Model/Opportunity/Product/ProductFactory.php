<?php

declare(strict_types = 1);

namespace App\Opportunity\Product;

use App\Customer\Customer;
use App\Helpers\Prices;
use App\Opportunity\Opportunity;
use App\Price\Price;
use App\Product\Product AS CatalogProduct;
use App\Product\Production\ProductionTimeDTO;
use App\Product\Ring\Size\Size;
use App\Product\WeedingRing\Gender\Gender;
use App\ProductParameterGroup\Lock\Parameter\Parameter;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductFactory
{


    /** @var ITranslator */
    protected $translator;



    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }



    /**
     * Create opportunity product.
     * @param $opportunity Opportunity
     * @param $catalogProduct CatalogProduct
     * @param $customer Customer|null
	 * @param $quantity int
     * @return Product
     */
    public function createFromCatalogProduct(Opportunity $opportunity, CatalogProduct $catalogProduct, Customer $customer = NULL, int $quantity) : Product
    {
        $translation = $catalogProduct->getTranslation();

        $product = new Product();
        $product->setOpportunityId($opportunity->getId());
        $product->setProductId($catalogProduct->getId());
        $product->setExternalSystemId($catalogProduct->getExternalSystemId());
        $product->setName($translation->getName());
        $product->setCode($catalogProduct->getCode());
        $product->setUrl($translation->getUrl());
        $product->setQuantity($quantity);
        $product->setDiscount($customer instanceof Customer && $catalogProduct->isDiscountAllowed() ? Customer::DISCOUNT : 0.0);
        $product->setPrice(Prices::subtractPercent($catalogProduct->getPrice(), $product->getDiscount()));
        $product->setVat($catalogProduct->getVat());
        $product->setStock((bool)$catalogProduct->getStock());

        return $product;
    }



    /**
     * @param $opportunity Opportunity
     * @param $catalogProduct CatalogProduct
     * @param $calculation Price
     * @param $gender Gender
     * @param $size Size
     * @param $productionTime ProductionTimeDTO
     * @param $customer Customer|null
     * @param $qualityDiamond Parameter|null
     * @return Product
     */
    public function createFromWeedingRing(Opportunity $opportunity,
                                          CatalogProduct $catalogProduct,
                                          Price $calculation,
                                          Gender $gender,
                                          Size $size,
                                          ProductionTimeDTO $productionTime,
                                          Customer $customer = NULL,
                                          Parameter $qualityDiamond = NULL) : Product
    {
        $translation = $catalogProduct->getTranslation();

        //todo parameters from this comment can be saved in opportunity_product_parameter database table
        $comment = sprintf('%s: %s.', $this->translator->translate('general.type.label'), $this->translator->translate($gender->getTypeValues()['translationKey']));
        $comment .= sprintf(' %s: %s.', $this->translator->translate('product.ring.size.label'), $size->getSize());
        $qualityDiamond !== NULL ? $comment .= sprintf(' %s: %s.', $this->translator->translate('product.diamond.quality.label'), $qualityDiamond->getValue()) : NULL;

        $product = new Product();
        $product->setOpportunityId($opportunity->getId());
        $product->setProductId($catalogProduct->getId());
        $product->setExternalSystemId($catalogProduct->getExternalSystemId());
        $product->setName($translation->getName());
        $product->setCode($catalogProduct->getCode());
        $product->setUrl($translation->getUrl());
        $product->setQuantity(1);
        $product->setPrice($calculation->summary);
        $product->setVat(CatalogProduct::WEEDING_RING_PAIR_TYPE_VAT);
        $product->setDiscount($customer instanceof Customer ? Customer::DISCOUNT : 0.0);
        $product->setStock(0);
        $product->setComment($comment);
        $product->setProductionTime($productionTime->getKey());
        if ($productionTime->hasSurcharge()) {
            $product->setProductionTimePercent($productionTime->getSurchargePercent());
        }

        return $product;
    }
}