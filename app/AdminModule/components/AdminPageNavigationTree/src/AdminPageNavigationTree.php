<?php

namespace App\Components\AdminPageNavigationTree;


use App\Components\LanguageMiniSwitcher\LanguageMiniSwitcher;
use App\Components\LanguageMiniSwitcher\LanguageMiniSwitcherFactory;
use App\Components\Tree\Item;
use App\Components\Tree\Sources\EntityParent\EntityParentSource;
use App\Components\Tree\Tree;
use App\Page\PageRepositoryFactory;
use Nette\Application\UI\Control;
use Nette\Utils\Html;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class AdminPageNavigationTree extends Control
{


    /** @var PageRepositoryFactory */
    protected $pageRepositoryFactory;

    /** @var LanguageMiniSwitcherFactory */
    protected $languageMiniSwitcherFactory;



    public function __construct(PageRepositoryFactory $pageRepositoryFactory,
                                LanguageMiniSwitcherFactory $languageMiniSwitcherFactory)
    {
        parent::__construct();
        $this->pageRepositoryFactory = $pageRepositoryFactory;
        $this->languageMiniSwitcherFactory = $languageMiniSwitcherFactory;
    }



    /**
     * @param int $langId
     * @return AdminPageNavigationTree
     */
    public function setLangId(int $langId) : self
    {
        $this->langId = $langId;
        return $this;
    }



    /**
     * @return Tree
     */
    public function createComponentTree() : Tree
    {
        $pageRepository = $this->pageRepositoryFactory->create();
        $pages = $pageRepository->findByLangId($this->getComponent("languageMiniSwitcher")->getLangId(), FALSE);

        $tree = new Tree();
        $tree->setSource(new EntityParentSource($pages ? $pages : NULL));
        $tree->setCustomRenderCallback(function (Item $item) {
            $el = Html::el("a");
            $el->href($this->presenter->link("Page:edit", [
                "id" => $item->getId()
            ]));
            $el->setText($item->getTitle());
            echo $el;
        });
        return $tree;
    }



    /**
     * @return LanguageMiniSwitcher
     */
    public function createComponentLanguageMiniSwitcher() : LanguageMiniSwitcher
    {
        return $this->languageMiniSwitcherFactory->create();
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }
}