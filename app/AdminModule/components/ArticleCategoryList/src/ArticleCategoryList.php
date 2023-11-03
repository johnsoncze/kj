<?php

namespace App\Components\ArticleCategoryList;

use App\Article\Module\Module;
use App\Article\Module\ModuleRepository;
use App\ArticleCategory\ArticleCategoryRepositoryFactory;
use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Entities;
use App\Language\LanguageListFacadeFactory;
use Grido\Grid;
use Nette\Utils\Arrays;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCategoryList extends GridoComponent
{


    /** @var LanguageListFacadeFactory */
    protected $languageListFacadeFactory;

    /** @var ModuleRepository */
    protected $moduleRepo;



    public function __construct(GridoFactory $gridoFactory,
                                ArticleCategoryRepositoryFactory $articleCategoryRepositoryFactory,
                                LanguageListFacadeFactory $languageListFacadeFactory,
                                ModuleRepository $moduleRepository)
    {
        parent::__construct($gridoFactory);
        $this->repositoryFactory = $articleCategoryRepositoryFactory;
        $this->languageListFacadeFactory = $languageListFacadeFactory;
        $this->moduleRepo = $moduleRepository;
    }



    /**
     * @return Grid
     */
    public function createComponentList()
    {
        $moduleList = $this->getModuleList();

        $list = $this->languageListFacadeFactory->create()->getList();
        $repository = $this->repositoryFactory->create();

        $source = new RepositorySource($repository);
        $source->setRepositoryMethod("findForList");

        $source->setDefaultSort("sort", "ASC");

        $grido = $this->gridoFactory->create();
        $grido->setModel($source);
        $grido->addColumnText("name", "Název")
            ->setSortable()
            ->setFilterText();
        $grido->addColumnText("languageId", "Jazyk")
            ->setReplacement($list)
            ->setSortable()
            ->setFilterSelect(Arrays::mergeTree(["" => ""], $list));
        $grido->addColumnText('moduleId', 'Modul')
            ->setCustomRender(function ($row) use ($moduleList) {
                return $moduleList[$row['moduleId']];
            })
            ->setFilterSelect(Arrays::mergeTree(['' => ''], $moduleList));
        $grido->addColumnText("articlesCount", "Počet článků")
            ->setSortable();
        $grido->addColumnDate("addDate", "Datum přidání")
            ->setDateFormat("d.m.Y H:i:s")
            ->setSortable();
        $grido->addActionHref("edit", null, "ArticleCategory:edit")
            ->setIcon("pencil");
        $grido->addActionHref("remove", null, "ArticleCategory:remove")
            ->setIcon("trash");
        $grido->getColumn("name")->getHeaderPrototype()->style["width"] = "30%";
        $grido->getColumn("moduleId")->getHeaderPrototype()->style["width"] = "15%";
        $grido->getColumn("languageId")->getHeaderPrototype()->style["width"] = "15%";
        $grido->getColumn("articlesCount")->getHeaderPrototype()->style["width"] = "10%";
        return $grido;
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }



    /**
     * @return Module[]|array
     */
    protected function getModuleList() : array
    {
        $modules = $this->moduleRepo->findAll();
        return $modules ? Entities::toPair($modules, 'id', 'name') : [];
    }
}