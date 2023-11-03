<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\SiteMap;

use App\FrontModule\Components\SiteMap\SiteMapFactory;
use App\Language\LanguageEntity;
use App\Page\PageRepository;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SiteMap extends Control
{


    /** @var string */
    const DEFAULT_TEMPLATE = __DIR__ . '/default.latte';

    /** @var PageRepository */
    private $pageRepo;

    /** @var SiteMapFactory */
    private $siteMapFactory;

    /** @var LanguageEntity|null */
    private $language;



    public function __construct(PageRepository $pageRepository,
                                SiteMapFactory $siteMapFactory)
    {
        parent::__construct();
        $this->pageRepo = $pageRepository;
        $this->siteMapFactory = $siteMapFactory;
    }



    /**
     * @param $language LanguageEntity
     * @return self
     */
    public function setLanguage(LanguageEntity $language) : self
    {
        $this->language = $language;
        return $this;
    }



    /**
     * @return \App\FrontModule\Components\SiteMap\SiteMap
     */
    public function createComponentSiteMap() : \App\FrontModule\Components\SiteMap\SiteMap
    {
        $pages = $this->pageRepo->findPublishedByLanguageId($this->language->getId());

        $siteMap = $this->siteMapFactory->create();
        $siteMap->setCacheId($this->getCacheId());
        foreach ($pages as $page) {
            $siteMap->addItem($page);
        }
        return $siteMap;
    }



    /**
     * @return string
     */
    public function getCacheId() : string
    {
        return 'page_sitemap_' . $this->language->getId();
    }



    public function render()
    {
        $this->template->setFile(self::DEFAULT_TEMPLATE);
        $this->template->render();
    }



    public function renderToString()
    {
        $this->template->setFile(self::DEFAULT_TEMPLATE);
        return (string)$this->template;
    }
}