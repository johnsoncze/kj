<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\SiteMap;

use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\FrontModule\Components\SiteMap\SiteMapFactory;
use App\Language\LanguageEntity;
use Nette\Application\UI\Control;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SiteMap extends Control
{


    /** @var string */
    const DEFAULT_TEMPLATE = __DIR__ . '/templates/default.latte';
    const PARAMETER_GROUP_TEMPLATE = __DIR__ . '/templates/parameterGroup.latte';

    /** @var CategoryFiltrationGroupRepository */
    private $categoryGroupRepo;

    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var SiteMapFactory */
    private $siteMapFactory;

    /** @var LanguageEntity|null */
    private $language;



    public function __construct(CategoryFiltrationGroupRepository $categoryFiltrationGroupRepo,
                                CategoryRepository $categoryRepo,
                                SiteMapFactory $siteMapFactory)
    {
        parent::__construct();
        $this->categoryGroupRepo = $categoryFiltrationGroupRepo;
        $this->categoryRepo = $categoryRepo;
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
        $categories = $this->findCategories();

        $siteMap = $this->siteMapFactory->create();
        $siteMap->setCacheId($this->getCacheId());
        foreach ($categories as $category) {
            $siteMap->addItem($category);
        }

        return $siteMap;
    }



    /**
     * @return CountDTO
     */
    public function getGroupCount() : CountDTO
    {
        return $this->categoryGroupRepo->getCountIndexedByLanguageId($this->language->getId());
    }



    /**
     * @param $limit int
     * @param $offset int
     * @return CategoryFiltrationGroupEntity[]|array
     */
    public function findGroups(int $limit, int $offset) : array
    {
        return $this->categoryGroupRepo->findIndexedByLanguageIdAndLimitAndOffset($this->language->getId(), $limit, $offset);
    }



    /**
     * @return CategoryEntity[]|array
     */
    public function findCategories() : array
    {
        return $this->categoryRepo->findPublishedByLanguageId($this->language->getId());
    }



    /**
     * @return string
     */
    public function getCacheId() : string
    {
        return 'category_sitemap_' . $this->language->getId();
    }



    /**
     * @return string
     */
    public function getGroupCacheId() : string
    {
        return 'categoryParameterGroupSitemap_' . $this->language->getId();
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



    public function renderParameterGroup()
    {
        $this->template->setFile(self::PARAMETER_GROUP_TEMPLATE);
        $this->template->render();
    }



    public function renderParameterGroupToString()
    {
        $this->template->setFile(self::PARAMETER_GROUP_TEMPLATE);
        return (string)$this->template;
    }
}