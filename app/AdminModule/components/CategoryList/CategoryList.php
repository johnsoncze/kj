<?php

declare(strict_types = 1);

namespace App\Components\CategoryList;

use App\Category\CategoryEntity;
use App\Category\CategoryRepositoryFactory;
use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Language\LanguageListFacadeFactory;
use Grido\Grid;
use Nette\Utils\Html;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryList extends GridoComponent
{


    /** @var CategoryRepositoryFactory */
    protected $categoryRepositoryFactory;

    /** @var LanguageListFacadeFactory */
    protected $languageListFacadeFactory;



    public function __construct(GridoFactory $gridoFactory,
                                CategoryRepositoryFactory $categoryRepositoryFactory,
                                LanguageListFacadeFactory $languageListFacadeFactory)
    {
        parent::__construct($gridoFactory);
        $this->categoryRepositoryFactory = $categoryRepositoryFactory;
        $this->languageListFacadeFactory = $languageListFacadeFactory;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        //language list
        $facade = $this->languageListFacadeFactory->create();
        $languagesList = $facade->getList();

        //Status list
        $statuses = CategoryEntity::getStatuses();
        $statusesList = Arrays::toPair($statuses, "key", "translate");

        //source
        $repo = $this->categoryRepositoryFactory->create();
        $model = new RepositorySource($repo);
        $model->setDefaultSort("sort", "ASC");

        //Grido
        $grid = $this->gridoFactory->create();
        $grid->setModel($model);

        $grid->addColumnText("name", "Název")
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText("languageId", "Jazyk")
            ->setReplacement($languagesList)
            ->setSortable()
            ->setFilterSelect(Arrays::mergeTree(["" => ""], $languagesList));
        $grid->addColumnText("parentCategoryId", "Nadřazená kategorie")
            ->setCustomRender(function ($row) {
                return $row["parentCategory"]["name"] ?? '-';
            })
            ->setSortable()
            ->setFilterText();
        $grid->addColumnDate("addDate", "Datum přidání")
            ->setDateFormat("d.m.Y H:i:s")
            ->setSortable();
        $grid->addColumnText("status", "Stav")
            ->setReplacement($statusesList)
            ->setSortable()
            ->setFilterSelect(Arrays::mergeTree(["" => ""], $statusesList));

        //Style..
        $grid->getColumn("name")->getHeaderPrototype()->style["width"] = "20%";
        $grid->getColumn("languageId")->getHeaderPrototype()->style["width"] = "15%";
        $grid->getColumn("parentCategoryId")->getHeaderPrototype()->style["width"] = "20%";
        $grid->getColumn("addDate")->getHeaderPrototype()->style["width"] = "15%";
        $grid->getColumn("status")->getHeaderPrototype()->style["width"] = "15%";

        $grid->addActionHref("edit", NULL, "Category:edit")
            ->setIcon("pencil");
        $grid->addActionHref("remove", NULL, "remove!")
            ->setIcon("trash")
            ->setConfirm(function ($row) {
                return sprintf("Opravdu si přejete smazat kategorii '%s' ?", $row["name"]);
            });

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }
}