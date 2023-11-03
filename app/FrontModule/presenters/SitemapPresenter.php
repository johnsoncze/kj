<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\Page\SiteMap\SiteMap;
use App\FrontModule\Components\Page\SiteMap\SiteMapFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SitemapPresenter extends AbstractLanguagePresenter
{


    /** @var \App\FrontModule\Components\Category\SiteMap\SiteMapFactory @inject */
    public $categorySiteMapFactory;

    /** @var SiteMapFactory @inject */
    public $pageSiteMapFactory;

    /** @var \App\FrontModule\Components\Product\SiteMap\SiteMapFactory @inject */
    public $productSiteMapFactory;



    /**
     * @return \App\FrontModule\Components\Category\SiteMap\SiteMap
     */
    public function createComponentCategorySiteMap() : \App\FrontModule\Components\Category\SiteMap\SiteMap
    {
        $siteMap = $this->categorySiteMapFactory->create();
        $siteMap->setLanguage($this->languageEntity);
        return $siteMap;
    }



    /**
     * @return SiteMap
     */
    public function createComponentPageSiteMap() : SiteMap
    {
        $siteMap = $this->pageSiteMapFactory->create();
        $siteMap->setLanguage($this->languageEntity);
        return $siteMap;
    }



    /**
     * @return \App\FrontModule\Components\Product\SiteMap\SiteMap
     */
    public function createComponentProductSiteMap() : \App\FrontModule\Components\Product\SiteMap\SiteMap
    {
        $siteMap = $this->productSiteMapFactory->create();
        $siteMap->setLanguage($this->languageEntity);
        return $siteMap;
    }

}