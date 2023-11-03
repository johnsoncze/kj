<?php

declare(strict_types = 1);

namespace App\Components\AdminModule\CategoryNavigationTree;

use App\Category\CategoryRepositoryFactory;
use App\Components\LanguageMiniSwitcher\LanguageMiniSwitcher;
use App\Components\LanguageMiniSwitcher\LanguageMiniSwitcherFactory;
use App\Components\Tree\Item;
use App\Components\Tree\Sources\EntityParent\EntityParentSource;
use App\Components\Tree\Tree;
use Nette\Application\UI\Control;
use Nette\Utils\Html;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryNavigationTree extends Control
{


    /** @var CategoryRepositoryFactory */
    protected $categoryRepositoryFactory;

    /** @var LanguageMiniSwitcherFactory */
    protected $languageMiniSwitcherFactory;



    /**
     * CategoryNavigationTree constructor.
     * @param $categoryRepositoryFactory
     * @param LanguageMiniSwitcherFactory $languageMiniSwitcherFactory
     */
    public function __construct(CategoryRepositoryFactory $categoryRepositoryFactory,
                                LanguageMiniSwitcherFactory $languageMiniSwitcherFactory)
    {
        parent::__construct();
        $this->categoryRepositoryFactory = $categoryRepositoryFactory;
        $this->languageMiniSwitcherFactory = $languageMiniSwitcherFactory;
    }



    /**
     * @return Tree
     */
    public function createComponentTree() : Tree
    {
        $languageId = $this->getComponent("languageMiniSwitcher")->getLangId();

        //get categories
        $repo = $this->categoryRepositoryFactory->create();
        $categories = $repo->findByLanguageId((int)$languageId);

        $tree = new Tree();
        $tree->setSource(new EntityParentSource($categories ? $categories : NULL));
        $tree->setCustomRenderCallback(function (Item $item) {
            $el = Html::el("a");
            $el->href($this->presenter->link("Category:edit", [
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