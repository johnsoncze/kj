<?php

declare(strict_types = 1);

namespace App\Components\PageList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Helpers\Entities;
use App\Language\LanguageRepositoryFactory;
use App\Page\PageEntity;
use App\Page\PageRemoveFacadeException;
use App\Page\PageRemoveFacadeFactory;
use App\Page\PageRepositoryFactory;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageList extends GridoComponent
{


    /** @var PageRepositoryFactory */
    protected $pageRepositoryFactory;

    /** @var PageRemoveFacadeFactory */
    protected $pageRemoveFacadeFactory;

    /** @var LanguageRepositoryFactory */
    protected $languageRepositoryFactory;

    /** @var Context */
    protected $database;



    public function __construct(GridoFactory $gridoFactory, PageRepositoryFactory $pageRepositoryFactory,
                                LanguageRepositoryFactory $languageRepositoryFactory,
                                PageRemoveFacadeFactory $pageRemoveFacadeFactory,
                                Context $database)
    {
        parent::__construct($gridoFactory);

        $this->pageRepositoryFactory = $pageRepositoryFactory;
        $this->languageRepositoryFactory = $languageRepositoryFactory;
        $this->pageRemoveFacadeFactory = $pageRemoveFacadeFactory;
        $this->database = $database;
    }



    /**
     * @return \Grido\Grid
     */
    public function createComponentList()
    {
        //Create language list
        $languageRepository = $this->languageRepositoryFactory->create();
        $languages = $languageRepository->findAll();
        $languageList = Entities::toPair($languages, "id", "name");

        //Status list
        $statuses = PageEntity::getStatuses();
        $statusesList = Arrays::toPair($statuses, "key", "translate");

        $pageRepository = $this->pageRepositoryFactory->create();
        $grid = $this->gridoFactory->create();

        $source = new RepositorySource($pageRepository);
        $source->setDefaultSort('name', 'ASC');

        $grid->setModel($source);
        $grid->addColumnText("name", "Název")
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText("languageId", "Jazyk")
            ->setReplacement($languageList)
            ->setSortable()
            ->setFilterSelect(Arrays::mergeTree(["" => ""], $languageList));
        $grid->addColumnDate("addDate", "Datum přidání")
            ->setDateFormat("d.m.Y H:i:s")
            ->setSortable();
        $grid->addColumnText("status", "Stav")
            ->setReplacement($statusesList)
            ->setSortable()
            ->setFilterSelect(Arrays::mergeTree(["" => ""], $statusesList));
        $grid->addActionHref("edit", "", "Page:Edit")
            ->setIcon("pencil");
        $grid->addActionHref("remove", null, "pageList:PageRemove!")
            ->setIcon("trash")
            ->setConfirm(function ($row) {
                return "Opravdu si přejete smazat stránku '" . $row["name"] . "'?";
            });

        //Style..
        $grid->getColumn("name")->getHeaderPrototype()->style["width"] = "40%";
        $grid->getColumn("languageId")->getHeaderPrototype()->style["width"] = "15%";
        $grid->getColumn("addDate")->getHeaderPrototype()->style["width"] = "15%";
        $grid->getColumn("status")->getHeaderPrototype()->style["width"] = "15%";

        return $grid;
    }



    /**
     * @param $id int
     * @return void
     */
    public function handlePageRemove(int $id)
    {
        try {
            $removeFacade = $this->pageRemoveFacadeFactory->create();

            //Remove
            $this->database->beginTransaction();
            $removeFacade->remove($id);
            $this->database->commit();

            $this->presenter->flashMessage("Stránka byla smazána.", "success");
        } catch (PageRemoveFacadeException $exception) {
            $this->database->rollback();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }

        $this->presenter->redirect("this");
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }
}