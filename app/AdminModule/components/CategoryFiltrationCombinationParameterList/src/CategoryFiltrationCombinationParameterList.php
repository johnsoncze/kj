<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\CategoryFiltrationCombinationParameterList;

use App\Category\CategoryEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRemoveFacadeException;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRemoveFacadeFactory;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepositoryFactory;
use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use Grido\Grid;
use Nette\Application\LinkGenerator;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationCombinationParameterList extends GridoComponent
{


    /** @var CategoryFiltrationGroupRemoveFacadeFactory */
    protected $categoryFiltrationGroupRemoveFacadeFactory;

    /** @var CategoryFiltrationGroupRepositoryFactory */
    protected $categoryFiltrationGroupRepositoryFactory;

    /** @var CategoryEntity|null */
    protected $categoryEntity;

    /** @var Context */
    protected $database;

    /** @var LinkGenerator */
    protected $linkGenerator;



    public function __construct(GridoFactory $gridoFactory,
                                CategoryFiltrationGroupRemoveFacadeFactory $categoryFiltrationGroupRemoveFacadeFactory,
                                CategoryFiltrationGroupRepositoryFactory $categoryFiltrationGroupRepositoryFactory,
                                Context $context,
                                LinkGenerator $linkGenerator)
    {
        parent::__construct($gridoFactory);
        $this->categoryFiltrationGroupRemoveFacadeFactory = $categoryFiltrationGroupRemoveFacadeFactory;
        $this->categoryFiltrationGroupRepositoryFactory = $categoryFiltrationGroupRepositoryFactory;
        $this->database = $context;
        $this->linkGenerator = $linkGenerator;
    }



    /**
     * @param CategoryEntity $categoryEntity
     * @return CategoryFiltrationCombinationParameterList
     */
    public function setCategoryEntity(CategoryEntity $categoryEntity) : self
    {
        $this->categoryEntity = $categoryEntity;
        return $this;
    }



    /**
     * @return CategoryEntity
     * @throws CategoryFiltrationCombinationParameterListException
     */
    public function getCategoryEntity() : CategoryEntity
    {
        if (!$this->categoryEntity instanceof CategoryEntity) {
            throw new CategoryFiltrationCombinationParameterListException(sprintf("You must set object '%s'.", CategoryEntity::class));
        }
        return $this->categoryEntity;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $indexList = [1 => 'Ano', 0 => 'Ne'];

        $repo = $this->categoryFiltrationGroupRepositoryFactory->create();
        $model = new RepositorySource($repo);
        $model->filter([["categoryId", "=", $this->getCategoryEntity()->getId()]]);
        $model->setDefaultSort(['LENGTH(sort)', 'sort'], 'ASC');

        $grid = $this->gridoFactory->create();
        $grid->setModel($model);

        //columns
        $grid->addColumnText('titleSeo', "Titulek")
            ->setFilterText();
        $grid->addColumnText("indexSeo", "Index")
            ->setSortable()
            ->setReplacement($indexList)
            ->setFilterSelect(Arrays::mergeTree(['' => ''], $indexList));
        $grid->addColumnText('showInMenu', 'Zobrazovat v menu')
            ->setSortable()
            ->setReplacement($indexList)
            ->setFilterSelect(Arrays::mergeTree(['' => ''], $indexList));

        //actions
        $grid->addActionHref('default', '', 'Category:editFiltrationCombinationEdit')//set another route because workaround
        ->setCustomRender(function (CategoryFiltrationGroupEntity $row) {
            $class = 'btn btn-default btn-xs btn-mini';
            if ($this->categoryEntity->isPublished()) {
                $row->setCategory($this->categoryEntity);
                $link = $row->getFrontendLink($this->linkGenerator);
                return sprintf('<a href="%s"
                                   class="%s"
                                   target="_blank"
                                   title="Zobrazit na frontendu"
                                   ><i class="fa fa-eye"></i></a>', $link, $class);
            }
            return sprintf('<span class="%s" title="Není možné zobrazit na frontendu, jelikož kategorie skupiny není aktivní."><i class="fa fa-eye-slash"></i></span>', $class);
        });
        $grid->addActionHref("edit", "", "Category:editFiltrationCombinationEdit")
            ->setCustomRender(function ($row) {
                $link = $this->getPresenter()->link('Category:editFiltrationCombinationEdit', ['id' => $row->getCategoryId(), 'combinationId' => $row->getId()]);
                return sprintf('<a href="%s"
                                   class="btn btn-default btn-xs btn-mini"
                                   ><i class="fa fa-pencil"></i></a>', $link);
            });
        $grid->addActionHref("remove", "", $this->getName() . "-filtrationCombinationRemove!")
            ->setCustomRender(function ($row) {
                $link = $this->link("filtrationCombinationRemove!", ['id' => $row->getCategoryId(), 'combinationId' => $row->getId()]);
                return sprintf('<a href="%s"
                                   class="btn btn-default btn-xs btn-mini"
                                   data-grido-confirm="Opravdu si přejte smazat kombinaci?"><i class="fa fa-trash"></i></a>', $link);
            });

        //styles
        $grid->getColumn('titleSeo')->getHeaderPrototype()->style["width"] = "50%";
        $grid->getColumn("indexSeo")->getHeaderPrototype()->style["width"] = "20%";
        $grid->getColumn("showInMenu")->getHeaderPrototype()->style["width"] = "20%";

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }



    /**
     * @param $combinationId int id of group
     */
    public function handleFiltrationCombinationRemove(int $combinationId)
    {
        try {
            $this->database->beginTransaction();
            $removeFacade = $this->categoryFiltrationGroupRemoveFacadeFactory->create();
            $removeFacade->remove($combinationId);
            $this->database->commit();
            $this->flashMessage("Kombinace byla smazána.", "success");
            $this->redirect("this");
        } catch (CategoryFiltrationGroupRemoveFacadeException $exception) {
            $this->database->rollBack();
            $this->flashMessage($exception->getMessage(), "danger");
        }
    }
}