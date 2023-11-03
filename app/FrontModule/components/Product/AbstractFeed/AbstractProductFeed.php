<?php

namespace App\FrontModule\Components\Product\AbstractProductFeed;

use App\Product\Product;
use App\Product\ProductDTO;
use App\Product\ProductDTOFactory;
use App\Product\ProductPublishedRepositoryFactory;
use Nette\Application\UI\Control;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;
use App\FrontModule\Components\Feed\AbstractFeed\AbstractFeed;

abstract class AbstractProductFeed extends AbstractFeed
{

    /** @var ProductPublishedRepositoryFactory */
    protected $productRepoFactory;

    /** @var ProductDTOFactory */
    protected $productDTOFactory;

    public function __construct(ProductPublishedRepositoryFactory $productRepositoryFactory,
                                ProductDTOFactory $productDTOFactory)
    {
        parent::__construct();
        ini_set("memory_limit","512M");
        $this->productRepoFactory = $productRepositoryFactory;
        $this->productDTOFactory = $productDTOFactory;
    }


    /**
     * @return CountDTO
     */
    public function getProductsCount(): CountDTO
    {
        $productRepo = $this->productRepoFactory->create();
        return $productRepo->getCountForProductFeed();
    }
    /**
     * @param $limit int
     * @param $offset int|null
     * @return array|Product[]
     */
    public function getProducts(int $limit, int $offset = NULL): array
    {
        $productRepo = $this->productRepoFactory->create();
        $products = $productRepo->findForProductFeed($limit, $offset);
        return $products ?: [];
    }

    /**
     * @param $limit int
     * @param $offset int|null
     * @return array|ProductDTO[]
     */
    public function getProductDtos(int $limit, int $offset = NULL): array
    {
        $products = $this->getProducts($limit, $offset);
        $productsDTO = $this->productDTOFactory->createFromProducts($products, TRUE, TRUE);
        return $productsDTO ?: [];
    }


}