<?php

declare(strict_types=1);

namespace App\FrontModule\Components\Product\GoogleMerchantOptimizedFeed;

use App\FrontModule\Components\Feed\AbstractFeed\AbstractFeed;
use App\Product\Product;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockFacade;
use App\ProductParameterGroup\Lock\LockFacadeFactory;
use Nette\Database\Context as NetteDatabase;
use Nette\Utils\DateTime;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class GoogleMerchantOptimizedFeed extends AbstractFeed
{
    /** @var string */
    const CACHE_TAG = 'googleMerchantOptimizedFeed';

    /**
     * @var NetteDatabase
     */
    public NetteDatabase $ndb;

    /**
     * @var LockFacade
     */
    public LockFacade $lockFacade;

    /**
     * @param NetteDatabase $ndb
     * @param LockFacadeFactory $lockFacadeFactory
     */
    public function __construct(NetteDatabase $ndb, LockFacadeFactory $lockFacadeFactory)
    {
        parent::__construct();
        $this->ndb = $ndb;
        $this->lockFacade = $lockFacadeFactory->create();
        $this->templatePath = __DIR__ . '/googleMerchant.latte';
    }

    public function render()
    {
        $this->setProductsDataToTemplate();
        parent::render();
    }

    public function renderToString(): string
    {
        $this->setProductsDataToTemplate();
        return parent::renderToString();
    }

    /**
     * @return void
     */
    private function setProductsDataToTemplate(): void
    {
        $products = $this->getProductsData(1000000);
        $this->template->products = $products;
        $this->template->customLabels = $this->getCustomLabels(
            array_map(function ($productRow) {
                return $productRow->p_id;
            }, $products)
        );
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getProductsData(int $limit = 0): array
    {
        return $this->ndb->query("
            SELECT product.p_id, product.p_code, pt_google_merchant_title, pt_name, p_google_merchant_category, p_google_merchant_brand_text, product_translation.pt_description,
                   product_translation.pt_url, product.p_stock, p_empty_stock_state, product.p_photo, product.p_price
            FROM product
            JOIN product_translation ON (product_translation.pt_product_id = product.p_id)
            
            WHERE product_translation.pt_language_id = 1
            AND product.p_state = ?
            AND product.p_sale_online = 1
            LIMIT ?
        ",
            Product::PUBLISH,
            $limit ?: PHP_INT_MAX
        )->fetchAll();
    }

    /**
     * @return string
     */
    public function getDateTimeStamp(): string
    {
        return (new DateTime())->format(DateTime::RFC3339);
    }


    /**
     * @param array $productIds
     * @return array
     */
    public function getCustomLabels(array $productIds): array
    {
        return $this->lockFacade->getByKeyAndMoreProductId(Lock::GOOGLE_MERCHANT_CUSTOM_LABEL_2, $productIds);
    }

    /**
     * @return string
     */
    public function getDefaultProductDescription(): string
    {
        return 'Každý náš šperk je originální, tak jako je jedinečný každý náš vztah. Je výsledkem poctivé ruční řemeslné práce, aby vydržel navždy.';
    }
}