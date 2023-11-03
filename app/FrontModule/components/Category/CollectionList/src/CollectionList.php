<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\CollectionList;

use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use App\Category\Product\Related\Product;
use App\FrontModule\Components\Product\Preview\PreviewFactory;
use App\Helpers\Entities;
use App\Language\LanguageEntity;
use App\Product\ProductDTO;
use App\Product\ProductFindFacadeFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CollectionList extends \Nette\Application\UI\Control
{


    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var LanguageEntity|null */
    private $language;

    /** @var ProductFindFacadeFactory */
    private $productFindFacadeFactory;

    /** @var PreviewFactory */
    private $productPreviewFactory;



    public function __construct(CategoryRepository $categoryRepository,
                                PreviewFactory $previewFactory,
                                ProductFindFacadeFactory $productFindFacadeFactory)
    {
        parent::__construct();
        $this->categoryRepo = $categoryRepository;
        $this->productPreviewFactory = $previewFactory;
        $this->productFindFacadeFactory = $productFindFacadeFactory;
    }



    /**
     * @param $language LanguageEntity
     * @return CollectionList
     */
    public function setLanguage(LanguageEntity $language) : self
    {
        $this->language = $language;
        return $this;
    }



    public function render()
    {
        $categories = $this->categoryRepo->findPublishedForCategorySliderByLanguageId($this->language->getId());
        $products = $this->getProducts($categories);

        //add components for show product previews
        foreach ($products as $category => $_products) {
            foreach ($_products as $product) {
                $name = 'product_' . $product->getProduct()->getId();
                $productPreview = $this->productPreviewFactory->create();
                $productPreview->setProduct($product);
                $this->addComponent($productPreview, $name);
            }
        }

        $this->template->categories = $categories;
        $this->template->products = $products;


        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @param $categories array
     * @return ProductDTO[][]|array
    */
    private function getProducts(array $categories) : array
    {
        $categoryId = Entities::getProperty($categories, 'id');
        $productFindFacade = $this->productFindFacadeFactory->create();
        return $productFindFacade->findRepresentativePublishedByMoreCategoryIdAndType($categoryId, Product::TYPE_HOMEPAGE);
    }



    /**
     * @param $category CategoryEntity
     * @return string|null
    */
    public function getBanner(CategoryEntity $category)
    {
        $banners = [
            20 => '/www/assets/front/user_content/images/collection/diva/diva-hp-365x560.jpg',
            33 => '/www/assets/front/user_content/images/collection/tricolor/tricolor-hp-365x560.jpg',
            35 => '/www/assets/front/user_content/images/collection/tolerance-zasnubni/tolerance-zasnubni-hp-365x560.jpg',
	        //Nasi motýli
            24 => '/www/assets/front/user_content/images/collection/motyli/nasi-motyli-hp-365x560.jpg',
	        //Tahitske kralovny
            34 => '/www/assets/front/user_content/images/collection/tahitskekralovny/tahitske-kralovny-hp-365x560.jpg',
	        //Kubistik
	        77 => '/www/assets/front/user_content/images/collection/kubistik/kubistik-hp-365x560.jpg',
        ];

        return $banners[$category->getId()] ?? NULL;
    }
}