<?php

declare(strict_types = 1);

namespace App\Components\AdminModule\CategoryFiltrationList;

use App\Category\CategoryEntity;
use App\CategoryFiltration\CategoryFiltrationRemoveFacadeException;
use App\CategoryFiltration\CategoryFiltrationRemoveFacadeFactory;
use App\CategoryFiltration\CategoryFiltrationRepositoryFactory;
use App\Components\AdminModule\CategoryFiltrationForm\CategoryFiltrationFormException;
use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use Grido\Grid;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationList extends GridoComponent
{


    /** @var CategoryFiltrationRemoveFacadeFactory */
    protected $categoryFiltrationRemoveFacadeFactory;

    /** @var CategoryFiltrationRepositoryFactory */
    protected $categoryFiltrationRepositoryFactory;

    /** @var CategoryEntity|null */
    protected $categoryEntity;

    /** @var Context */
    protected $database;

    /** @var LocalizationResolver */
    protected $localizationResolver;



    public function __construct(Context $context,
                                GridoFactory $gridoFactory,
                                CategoryFiltrationRepositoryFactory $categoryFiltrationRepositoryFactory,
                                CategoryFiltrationRemoveFacadeFactory $categoryFiltrationRemoveFacadeFactory)
    {
        parent::__construct($gridoFactory);
        $this->database = $context;
        $this->categoryFiltrationRepositoryFactory = $categoryFiltrationRepositoryFactory;
        $this->categoryFiltrationRemoveFacadeFactory = $categoryFiltrationRemoveFacadeFactory;
        $this->localizationResolver = new LocalizationResolver();
    }



    /**
     * @param $categoryEntity CategoryEntity
     * @return CategoryFiltrationList
     */
    public function setCategoryEntity(CategoryEntity $categoryEntity) : self
    {
        $this->categoryEntity = $categoryEntity;
        return $this;
    }



    /**
     * @return CategoryEntity
     * @throws CategoryFiltrationFormException
     */
    public function getCategoryEntity() : CategoryEntity
    {
        if (!$this->categoryEntity instanceof CategoryEntity) {
            throw new CategoryFiltrationFormException(sprintf("You must set '%s' object.",
                CategoryEntity::class));
        }

        return $this->categoryEntity;
    }



    /**
     * todo sloupce nahradit daty z entity
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $language = $this->localizationResolver->getDefault();

        //model
        $repo = $this->categoryFiltrationRepositoryFactory->create();
        $model = new RepositorySource($repo);
        $model->setMethodCount('countJoined');
        $model->setRepositoryMethod('findJoined');
        $model->setDefaultSort("sort", "ASC");
        $model->filter([
            sprintf('cf_category_id = %s', $this->getCategoryEntity()->getId()), //only for selected category
            sprintf('ppgt_language_id = %s', $language->getId()),
        ]);

        $grid = $this->gridoFactory->create();
        $grid->setModel($model);
        $name = $grid->addColumnText("ppgt_name", "Skupina parametrů");
        $name->setSortable()->setFilterText();

        //actions
        $grid->addActionHref('detail', '', 'ProductParameterGroup:default') //workaround route
            ->setCustomRender(function ($row) {
                $link = $this->getPresenter()->link('ProductParameterGroup:edit', ['id' => $row['cf_product_parameter_group_id']]);
                return sprintf('<a href="%s" 
                                       class="btn btn-default btn-xs btn-mini" 
                                       ><i class="fa fa-eye"></i></a>', $link);
            });
        $grid->addActionHref("remove", "", $this->getName() . "-filtrationRemove!")
            ->setCustomRender(function ($row) {
                $link = $this->link('filtrationRemove!', ['id' => $row['cf_category_id'], 'filtrationId' => $row['cf_id']]);
                $confirm = sprintf('Opravdu si přejete smazat filtraci \'%s\'?', $row['ppgt_name']);
                return sprintf('<a href="%s" 
                                   class="grid-action-removeVariant btn btn-default btn-xs btn-mini" 
                                   data-grido-confirm="%s"><i class="fa fa-trash"></i></a>', $link, $confirm);
            });

        //styles
        $name->getHeaderPrototype()->style["width"] = "40%";

        //settings
        $grid->setPrimaryKey('cf_id');

        return $grid;
    }



    /**
     * @throws CategoryFiltrationFormException
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }



    /**
     * @param $filtrationId int
     */
    public function handleFiltrationRemove(int $filtrationId)
    {
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $facade = $this->categoryFiltrationRemoveFacadeFactory->create();
            $facade->remove($filtrationId);
            $this->database->commit();

            $presenter->flashMessage("Filtrace byla smazána.", "success");
            $presenter->redirect("this");
        } catch (CategoryFiltrationRemoveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), "danger");
        }
    }
}