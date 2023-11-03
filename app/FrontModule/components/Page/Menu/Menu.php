<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\Menu;

use App\Language\LanguageEntity;
use App\Page\PageEntity;
use App\Page\PageRepository;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Menu extends Control
{


    /** @var LanguageEntity|null */
    private $language;

    /** @var PageRepository */
    private $pageRepo;



    public function __construct(PageRepository $pageRepo)
    {
        parent::__construct();
        $this->pageRepo = $pageRepo;
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



    public function renderOurWeb()
    {
        $this->template->pages = $this->pageRepo->findPublishedByLanguageIdAndMenu($this->language->getId(), PageEntity::MENU_LOCATION_FOOTER_OUR_WEB);
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    public function renderPurchase()
    {
        $this->template->pages = $this->pageRepo->findPublishedByLanguageIdAndMenu($this->language->getId(), PageEntity::MENU_LOCATION_FOOTER_PURCHASE);
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}