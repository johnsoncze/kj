<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Category\CategoryEntity;
use App\Customer\Customer;
use App\FrontModule\Components\Breadcrumb\Item;
use App\FrontModule\Components\OpportunityForm\Data;
use App\FrontModule\Components\OpportunityForm\OpportunityForm;
use App\FrontModule\Components\Product\BenefitList\BenefitList;
use App\FrontModule\Components\Product\BenefitList\BenefitListFactory;
use App\FrontModule\Components\Product\Collection\Preview\Preview;
use App\FrontModule\Components\Product\Collection\Preview\PreviewFactory;
use App\FrontModule\Components\Product\Information\Information;
use App\FrontModule\Components\Product\Information\InformationFactory;
use App\FrontModule\Components\Product\MetaSmallBlock\MetaSmallBlock;
use App\FrontModule\Components\Product\MetaSmallBlock\MetaSmallBlockFactory;
use App\FrontModule\Components\Product\OrderBlock\OrderBlock;
use App\FrontModule\Components\Product\OrderBlock\OrderBlockFactory;
use App\FrontModule\Components\Product\PhotoGallery\PhotoGallery;
use App\FrontModule\Components\Product\PhotoGallery\PhotoGalleryFactory;
use App\FrontModule\Components\Product\PriceInfo\PriceInfo;
use App\FrontModule\Components\Product\PriceInfo\PriceInfoFactory;
use App\FrontModule\Components\Product\ProductionForm\ProductionForm;
use App\FrontModule\Components\Product\ProductionForm\ProductionFormFactory;
use App\FrontModule\Components\Product\ProductList\ProductListFactory;
use App\FrontModule\Components\Product\RelatedList\RelatedList;
use App\FrontModule\Components\Product\RelatedList\RelatedListFactory;
use App\FrontModule\Components\Product\SimilarCategoryList\SimilarCategoryList;
use App\FrontModule\Components\Product\SimilarCategoryList\SimilarCategoryListFactory;
use App\FrontModule\Components\Product\StockInfo\StockInfo;
use App\FrontModule\Components\Product\StockInfo\StockInfoFactory;
use App\FrontModule\Components\Product\VariantList\VariantList;
use App\FrontModule\Components\Product\VariantList\VariantListFactory;
use App\FrontModule\Components\Product\WeedingRing\Demand\Demand;
use App\FrontModule\Components\Product\WeedingRing\Demand\DemandFactory;
use App\FrontModule\Components\ShoppingCart\Benefit\Benefit;
use App\FrontModule\Components\ShoppingCart\Benefit\BenefitFactory;
use App\FrontModule\Components\ShoppingCart\CrossSelling\ProductList;
use App\Libs\FileManager\FileManager;
use App\Opportunity\Opportunity;
use App\Product\Product;
use App\Product\ProductDTO;
use App\Product\ProductFindFacadeFactory;
use App\Product\Production\Time\Time;
use App\Product\ProductNotFoundException;
use App\Product\ProductPublishedRepository;
use App\Product\Related\Related;
use App\Remarketing\Code\CodeDTO;
use App\ShoppingCart\Product\ShoppingCartProduct;
use App\ShoppingCart\Product\ShoppingCartProductSaveFacadeException;
use App\ShoppingCart\Product\ShoppingCartProductSaveFacadeFactory;
use App\ShoppingCart\ShoppingCartSaveFacadeException;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Utils\Strings;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductPresenter extends AbstractPresenter
{


    /** @var BenefitListFactory @inject */
    public $benefitListFactory;

    /** @var PreviewFactory @inject */
    public $collectionPreviewFactory;

    /** @var \App\FrontModule\Components\ShoppingCart\CrossSelling\ProductListFactory @inject */
    public $crossSellingFactory;

    /** @var InformationFactory @inject */
    public $informationFactory;

    /** @var MetaSmallBlockFactory @inject */
    public $metaSmallBlockFactory;

    /** @var OrderBlockFactory @inject */
    public $orderBlockFactory;

    /** @var PhotoGalleryFactory @inject */
    public $photoGalleryFactory;

    /** @var PriceInfoFactory @inject */
    public $priceBlockFactory;

    /** @var ProductDTO|null */
    private $productDTO;

    /** @var ProductFindFacadeFactory @inject */
    public $productFindFacadeFactory;

    /** @var ProductionFormFactory @inject */
    public $productionFormFactory;

    /** @var ProductListFactory @inject */
    public $productListFactory;

    /** @var ProductPublishedRepository @inject */
    public $productRepo;

    /** @var RelatedListFactory @inject */
    public $relatedListFactory;

    /** @var ShoppingCartProduct|null */
    private $shoppingCartAddedProduct;

    /** @var ShoppingCartProductSaveFacadeFactory @inject */
    public $shoppingCartProductFacadeFactory;

    /** @var SimilarCategoryListFactory @inject */
    public $similarCategoryListFactory;

    /** @var BenefitFactory @inject */
    public $shoppingCartBenefitFactory;

    /** @var StockInfoFactory @inject */
    public $stockInfoFactory;

    /** @var VariantListFactory @inject */
    public $variantListFactory;

    /** @var DemandFactory @inject */
    public $weedingRingDemandFactory;

    /** @var FileManager @inject */
    public $fileManeger;



    /**
     * @param $url string
     * @throws BadRequestException
     */
    public function actionDetail(string $url)
    {
        try {
            $urlChecked = Strings::webalize($url);
            if ($urlChecked != $url) {
                $this->redirect('detail', $urlChecked);
            }
            $url = Strings::webalize($url);
            $productFindFacade = $this->productFindFacadeFactory->create();
            $this->productDTO = $productFindFacade->getOnePublishedByUrl($url);

            //breadcrumb
            $this->productDTO->getCategory() ? $this->setBreadcrumb($this->productDTO->getCategory(), $this->productDTO->getProduct()) : NULL;

            //remarketing code
            $this->remarketingCode->setPageType(CodeDTO::PAGE_TYPE_PRODUCT);
            $this->remarketingCode->setData([
                CodeDTO::DATA_PRODID_KEY => $this->productDTO->getProduct()->getCode(),
                CodeDTO::DATA_TOTALVALUE_KEY => (float)number_format($this->loggedUser ? $this->productDTO->getProduct()->getPriceAfterDiscountWithoutVat(Customer::DISCOUNT) : $this->productDTO->getProduct()->getPriceWithoutVat(), 2, '.', ''),
                CodeDTO::DATA_CATEGORY => $this->productDTO->getCategory() ? $this->productDTO->getCategory()->getTextNavigation() : NULL,
            ]);

            $this->template->product = $this->productDTO->getProduct();
            $this->template->productDTO = $this->productDTO;
            $this->template->title = $this->productDTO->getProduct()->getTranslation()->getResolvedTitle($this->productDTO->getProduct()) . ' - ' . $this->productDTO->getProduct()->getCode();
            $this->template->metaDescription = $this->productDTO->getProduct()->getTranslation()->getDescriptionSeo();

            $this->template->ogTitle = $this->productDTO->getProduct()->getTranslation()->getTitleOg();
            $this->template->ogDescription = $this->productDTO->getProduct()->getTranslation()->getDescriptionOg();

            if ($this->template->product->getPhoto()) {
                $this->template->ogImage = $this->fileManeger->getThumbnail($this->template->product->getPhoto(), 800, 800);
            }

        } catch (ProductNotFoundException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
     * @param $id int
     * @return void
     * @throws BadRequestException
     */
    public function actionAddedIntoShoppingCart(int $id)
    {
        if (!$this->shoppingCart || (!$product = $this->shoppingCart->getProductDTOByProductId($id))) {
            try {
                /** @var Product $product */

                $product = $this->productRepo->getOneById($id, $this->translator);
                $this->redirect(':Front:Product:detail', $product->getTranslation()->getUrl());
            } catch (ProductNotFoundException $e) {
                throw new BadRequestException(null, 404);
            }
        }

        $this->template->product = $product;
        $this->template->shoppingCartProduct = $this->shoppingCartAddedProduct = $this->shoppingCart->getProducts()[$id];

        $this->template->index = FALSE;
        $this->template->title = $this->translator->translate('presenterFront.product.addedIntoShoppingCart');
    }



    /**
     * Action "addToShoppingCart"
	 * @param $productionTime string|null
     * @return void
     * @throws AbortException
     */
    public function handleAddToShoppingCart(string $productionTime = NULL)
    {
        try {
            $productId = $this->productDTO->getProduct()->getId();
            $customerId = $this->loggedUser ? $this->loggedUser->getEntity()->getId() : NULL;
            $productFacade = $this->shoppingCartProductFacadeFactory->create();
            $cart = $this->shoppingCart ? $this->shoppingCart->getEntity() : $this->shoppingCartSaveFacade->get($customerId);

            $this->database->beginTransaction();
            $productFacade->save((int)$cart->getId(), $productId, 1, $productionTime ? Time::resolveId($productionTime) : NULL);
            $this->database->commit();

            $this->redirect('Product:addedIntoShoppingCart', ['id' => $productId]);
        } catch (ShoppingCartSaveFacadeException $exception) {
            $this->database->rollBack();
            $this->flashMessage($exception->getMessage(), 'danger');
        } catch (ShoppingCartProductSaveFacadeException $exception) {
            $this->database->rollBack();
            $this->flashMessage($exception->getMessage(), 'danger');
        }

        $this->redirect('this');
    }



    /**
     * @return BenefitList
     */
    public function createComponentBenefitList() : BenefitList
    {
        $list = $this->benefitListFactory->create();
        $list->setProduct($this->productDTO->getProduct());
        return $list;
    }



    /**
     * @return Preview
     */
    public function createComponentCollectionPreview() : Preview
    {
        $preview = $this->collectionPreviewFactory->create();
        $preview->setProduct($this->productDTO->getProduct());
        return $preview;
    }



    /**
     * @return ProductList
     */
    public function createComponentCrossSelling() : ProductList
    {
        $list = $this->crossSellingFactory->create();
        $this->loggedUser ? $list->setCustomer($this->loggedUser->getEntity()) : NULL;
        $list->setShoppingCart($this->shoppingCart, $this->shoppingCartAddedProduct);
        return $list;
    }



    /**
     * @return OpportunityForm
     */
    public function createComponentDemandForm() : OpportunityForm
    {
        $form = $this->opportunityFormFactory->create();
        $form->addProduct(new \App\FrontModule\Components\OpportunityForm\Product($this->productDTO));
        $form->setPageArguments(['url' => $this->getParameter('url'), 'lang' => $this->getParameter('lang')]);
        $form->setType(Opportunity::TYPE_PRODUCT_DEMAND);
        $data = $this->loggedUser ? Data::createFromCustomer($this->loggedUser->getEntity()) : new Data();
        $comment = $this->productDTO->getState()->isProduction() && !$this->productDTO->getProduct()->isInStock()
			? $this->translator->translate('form.demand.input.comment.timeValue', ['name' => $this->productDTO->getProduct()->getTranslation()->getName()])
			: $this->translator->translate('form.demand.input.comment.value', ['name' => $this->productDTO->getProduct()->getTranslation()->getName()]);
        $data->setComment($comment);
        $form->setData($data);
        return $form;
    }



    /**
     * @return Information
     */
    public function createComponentInformation() : Information
    {
        $info = $this->informationFactory->create();
        $info->setProduct($this->productDTO);
        return $info;
    }



    /**
     * @return MetaSmallBlock
     */
    public function createComponentMetaSmallBlock() : MetaSmallBlock
    {
        $block = $this->metaSmallBlockFactory->create();
        $block->setProduct($this->productDTO->getProduct());
        $this->loggedUser ? $block->setCustomer($this->loggedUser->getEntity()) : NULL;
        return $block;
    }



    /**
     * @return OrderBlock
     */
    public function createComponentOrderBlock() : OrderBlock
    {
        $block = $this->orderBlockFactory->create();
        $this->loggedUser ? $block->setCustomer($this->loggedUser->getEntity()) : NULL;
        $block->setProduct($this->productDTO);
        return $block;
    }



    /**
     * @return PhotoGallery
     */
    public function createComponentPhotoGallery() : PhotoGallery
    {
        $gallery = $this->photoGalleryFactory->create();
        $gallery->setProduct($this->productDTO->getProduct());
		if ($this->productDTO->getCategory()){
            $gallery->setDisplayPackageImage($this->productDTO->getCategory()->getDisplayPackageImage());
		}else{
			$gallery->setDisplayPackageImage('');
		}
        return $gallery;
    }



    /**
     * @return PriceInfo
     */
    public function createComponentPriceBlock() : PriceInfo
    {
        $price = $this->priceBlockFactory->create();
        $price->setProduct($this->productDTO);
        $this->loggedUser ? $price->setCustomer($this->loggedUser->getEntity()) : NULL;
        return $price;
    }



    /**
	 * @return ProductionForm
    */
	public function createComponentProductionForm() : ProductionForm
	{
		$form = $this->productionFormFactory->create();
		$form->setProduct($this->productDTO);
		$this->loggedUser ? $form->setCustomer($this->loggedUser->getEntity()) : NULL;
		return $form;
	}



    /**
     * @return RelatedList
     */
    public function createComponentProductSetList() : RelatedList
    {
        $list = $this->relatedListFactory->create();
        $list->setProduct($this->productDTO->getProduct());
        $this->loggedUser ? $list->setCustomer($this->loggedUser->getEntity()) : NULL;
        return $list;
    }



    /**
	 * @return RelatedList
    */
    public function createComponentProductSimilarList() : RelatedList
	{
		$list = $this->relatedListFactory->create();
		$list->setType(Related::SIMILAR);
		$list->setProduct($this->productDTO->getProduct());
		$this->loggedUser ? $list->setCustomer($this->loggedUser->getEntity()) : NULL;
		return $list;
	}



    /**
     * @return VariantList
     */
    public function createComponentProductVariantList() : VariantList
    {
        $list = $this->variantListFactory->create();
        $list->setProduct($this->productDTO->getProduct());
        return $list;
    }



    /**
     * @return OpportunityForm
     */
    public function createComponentShowOnStoreForm() : OpportunityForm
    {
        $form = $this->opportunityFormFactory->create();
        $form->addProduct(new \App\FrontModule\Components\OpportunityForm\Product($this->productDTO));
        $form->setPageArguments(['url' => $this->getParameter('url'), 'lang' => $this->getParameter('lang')]);
        $form->setType(Opportunity::TYPE_PRODUCT_STORE_MEETING);
        $data = $this->loggedUser ? Data::createFromCustomer($this->loggedUser->getEntity()) : new Data();
        $data->setComment($this->translator->translate('form.opportunity.storeMeeting.product.comment.value', ['name' => $this->productDTO->getProduct()->getTranslation()->getName()]));
        $form->setData($data);
        return $form;
    }



    /**
     * @return SimilarCategoryList
     */
    public function createComponentSimilarCategoryList() : SimilarCategoryList
    {
        $list = $this->similarCategoryListFactory->create();
        $list->setProduct($this->productDTO->getProduct());
        $list->setLanguage($this->languageEntity);
        return $list;
    }



    /**
     * @return Benefit
     */
    public function createComponentShoppingCartBenefit() : Benefit
    {
        return $this->shoppingCartBenefitFactory->create();
    }



    /**
     * @return StockInfo
     */
    public function createComponentStockInfo() : StockInfo
    {
        $info = $this->stockInfoFactory->create();
        $info->setProduct($this->productDTO);
        return $info;
    }



    /**
     * @return Demand
     */
    public function createComponentWeedingRingDemand() : Demand
    {
        $demand = $this->weedingRingDemandFactory->create();
        $demand->setProduct($this->productDTO->getProduct());
        $this->loggedUser ? $demand->setCustomer($this->loggedUser->getEntity()) : NULL;
        return $demand;
    }



    /**
     * @param $category CategoryEntity
     * @param $product Product
     * @param $iterator int
     * @return CategoryEntity
     */
    private function setBreadcrumb(CategoryEntity $category, Product $product, int &$iterator = -1) : CategoryEntity
    {
        //recursion
        if ($category->getParentCategory()) {
            $iterator--;
            $this->setBreadcrumb($category->getParentEntity(), $product, $iterator);
        }

        $this->breadcrumb->addItem(new Item($category->getName(), $this->link('Category:default', ['url' => $category->getUrl()])));
        $iterator++;

        //add product in last iteration
        if ($iterator === 0) {
            $this->breadcrumb->addItem(new Item($product->getTranslation()->getName()));
        }

        return $category;
    }

}