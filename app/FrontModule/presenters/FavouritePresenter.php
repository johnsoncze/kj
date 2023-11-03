<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Helpers\Entities;
use App\FrontModule\Components\Favourite\FavouriteHelper;
use Nette\Http\Response;
use Nette\Http\Request;
use App\Product\Product;
use Nette\Database\Connection;
use App\Product\ProductDTO;
use App\Product\ProductDTOFactory;

use App\FrontModule\Components\Product\ProductList\ProductList;
use App\FrontModule\Components\Product\ProductList\ProductListFactory;
use App\Product\ProductPublishedRepositoryFactory;
use App\Product\ProductPublishedRepository;


final class FavouritePresenter extends AbstractPresenter
{

		/** @var ProductRepoFactory */
		protected $productRepoFactory;

    /** @var ProductListFactory */
    private $productListFactory;	
		
    /** @var ProductDTOFactory */
    private $productDTOFactory;		
		
    public function __construct(ProductPublishedRepositoryFactory $productRepositoryFactory,
																ProductListFactory $productListFactory,
																ProductDTOFactory $productDTOFactory)
    {
        $this->productRepoFactory = $productRepositoryFactory;
        $this->productListFactory = $productListFactory;
        $this->productDTOFactory = $productDTOFactory;
		}
		


    /**
     * @return ProductList
     */
    public function createComponentProductList() : ProductList
    {
				$favouriteHelper = new FavouriteHelper();

				$favouriteProductsId = $favouriteHelper->GetFavouriteProducts($this->getHttpRequest());

				$productPublishedRepository = $this->productRepoFactory->create();
				$favouriteProducts = array();
				foreach ($favouriteProductsId as $favouriteProductId) {
						$product = $productPublishedRepository->getOneByIdNoE((int)$favouriteProductId);
						if ($product !== null) {
								$favouriteProducts[] = $product;
						}
				}
			
				$_products = $this->productDTOFactory->createFromProducts($favouriteProducts);			
				
        $list = $this->productListFactory->create();
        $list->setProducts($_products);
        return $list;
    }
		
		
		
    public function actionDefault()
    {
        $title = $this->translator->translate('favourites.title');
        $this->template->title = $title;
				
				$favouriteHelper = new FavouriteHelper();
				$favouriteProductsId = $favouriteHelper->GetFavouriteProducts($this->getHttpRequest());

				$this->template->isFavouriteProducts = count($favouriteProductsId) ? 1 : 0;
				
				
				
		}

		
    /**
     * @param $id int
     */		
		public function actionAdd(int $id): void
		{
				if ($this->isAjax()) {	
						$favouriteHelper = new FavouriteHelper();
						
						$favouriteProducts = $favouriteHelper->AddFavouriteProduct($this->getHttpRequest(), $this->getHttpResponse(), $id);
						
						$response = new \stdClass();
						$response->state = "ok";
						$response->favouriteCount = count($favouriteProducts);
					
						$this->sendJson($response);
				}
		}
		
    /**
     * @param $id int
     */		
		public function actionRemove(int $id): void
		{
				if ($this->isAjax()) {	
						$favouriteHelper = new FavouriteHelper();
						
						$favouriteProducts = $favouriteHelper->RemoveFavouriteProduct($this->getHttpRequest(), $this->getHttpResponse(), $id);
						
						$response = new \stdClass();
						$response->state = "ok";
						$response->favouriteCount = count($favouriteProducts);
					
						$this->sendJson($response);
				}
		}		
		


}