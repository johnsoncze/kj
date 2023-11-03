<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\Feed\AbstractFeed\AbstractFeed;
use App\FrontModule\Components\Product\FacebookOptimizedFeed\FacebookOptimizedFeed;
use App\FrontModule\Components\Product\FacebookOptimizedFeed\FacebookOptimizedFeedFactory;
use App\FrontModule\Components\Product\GoogleMerchantFeed\GoogleMerchantFeed;
use App\FrontModule\Components\Product\GoogleMerchantFeed\GoogleMerchantFeedFactory;
use App\FrontModule\Components\Product\GoogleMerchantOptimizedFeed\GoogleMerchantOptimizedFeed;
use App\FrontModule\Components\Product\GoogleMerchantOptimizedFeed\GoogleMerchantOptimizedFeedFactory;
use App\FrontModule\Components\Product\HeurekaFeed\HeurekaFeed;
use App\FrontModule\Components\Product\HeurekaFeed\HeurekaFeedFactory;
use App\FrontModule\Components\Product\HeurekaOptimizedFeed\HeurekaOptimizedFeed;
use App\FrontModule\Components\Product\HeurekaOptimizedFeed\HeurekaOptimizedFeedFactory;
use App\FrontModule\Components\Product\ZboziCzFeed\ZboziCzFeed;
use App\FrontModule\Components\Product\ZboziCzFeed\ZboziCzFeedFactory;
use Nette\Application\Responses\FileResponse;
use Nette\Http\Response;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class FeedPresenter extends AbstractLanguagePresenter
{


    /** @var GoogleMerchantFeedFactory @inject */
    public $googleMerchantFeedFactory;

    /** @var GoogleMerchantOptimizedFeedFactory @inject */
    public $googleMerchantOptimizedFeedFactory;

    /** @var FacebookOptimizedFeedFactory @inject */
    public $facebookOptimizedFeedFactory;

    /** @var HeurekaFeedFactory @inject */
    public $heurekaFeedFactory;

    /** @var HeurekaOptimizedFeedFactory @inject */
    public $heurekaOptimizedFeedFactory;

    /** @var ZboziCzFeedFactory @inject */
    public $zboziCzFeedFactory;

    /**
     * @return GoogleMerchantFeed
     */
    public function createComponentGoogleMerchantFeed(): AbstractFeed
    {
        // return $this->googleMerchantFeedFactory->create();
        return $this->googleMerchantOptimizedFeedFactory->create();
    }

    /**
     * @return FacebookOptimizedFeed
     */
    public function createComponentFacebookFeed(): AbstractFeed
    {
        return $this->facebookOptimizedFeedFactory->create();
    }


    /**
     * @return HeurekaOptimizedFeed
     */
    public function createComponentHeurekaFeed(): AbstractFeed
    {
        // return $this->heurekaFeedFactory->create();
        return $this->heurekaOptimizedFeedFactory->create();
    }


    /**
     * @return ZboziCzFeed
     */
    public function createComponentZboziCzFeed(): ZboziCzFeed
    {
        return $this->zboziCzFeedFactory->create();
    }
}