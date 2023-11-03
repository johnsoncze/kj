<?php

declare(strict_types=1);

namespace App\FrontModule\Components\Product\GoogleMerchantFeed;

use App\FrontModule\Components\Product\AbstractProductFeed\AbstractProductFeed;
use App\Helpers\Entities;
use App\Product\Product;
use App\Product\ProductDTOFactory;
use App\Product\ProductPublishedRepositoryFactory;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockFacade;
use App\ProductParameterGroup\Lock\LockFacadeFactory;
use Nette\Application\UI\Control;
use Nette\Utils\DateTime;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class GoogleMerchantFeed extends AbstractProductFeed
{


    /** @var string */
    const CACHE_TAG = 'googleMerchantFeed';

    /** @var LockFacade */
    protected $lockFacade;

    public function __construct(LockFacadeFactory $lockFacadeFactory,
                                ProductPublishedRepositoryFactory $productRepositoryFactory,
                                ProductDTOFactory $productDTOFactory)
    {
        parent::__construct($productRepositoryFactory, $productDTOFactory);
        $this->templatePath = __DIR__ . '/templates/googleMerchant.latte';
        $this->lockFacade = $lockFacadeFactory->create();
    }


    /**
     * @return string
     */
    public function getDateTimeStamp(): string
    {
        return (new DateTime())->format(DateTime::RFC3339);
    }


    /**
     * @param $products Product[]
     * @return array
     */
    public function getCustomLabel1(array $products): array
    {
        $productId = Entities::getProperty($products, 'id');
        return $this->lockFacade->getByKeyAndMoreProductId(Lock::GOOGLE_MERCHANT_CUSTOM_LABEL_2, $productId);
    }

    /**
     * @param string $productName
     * @return int
     */
    public static function guessGoogleProductCategoryByProductName(string $productName): int
    {
        $productName = mb_strtolower($productName);
        if(str_contains($productName, 'natahovač')) {
            return 6870; // Apparel & Accessories > Jewelry > Watch Accessories > Watch Winders
        }
        if(str_contains($productName, 'prsten')) {
            return 200; // Apparel & Accessories > Jewelry > Rings
        }
        if(str_contains($productName, 'hodinky')) {
            return 201; // Apparel & Accessories > Jewelry > Watches
        }
        return 188; // Apparel & Accessories > Jewelry
    }
}