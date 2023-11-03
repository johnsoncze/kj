<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\CategoryParameterList;

use App\AdminModule\Components\ProductParameterSetList\ProductParameterSetList;
use App\Category\CategoryEntity;
use App\CategoryProductParameter\CategoryProductParameterEntity;
use App\CategoryProductParameter\CategoryProductParameterRepository;
use App\CategoryProductParameter\CategoryProductParameterSaveFacadeException;
use App\CategoryProductParameter\CategoryProductParameterSaveFacadeFactory;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Product\Parameter\ParameterStorageFacadeFactory;
use App\Product\Parameter\ProductParameterRepository;
use App\ProductParameter\ProductParameterTranslationEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepository;
use Grido\Grid;
use Nette\Database\Context;


final class CategoryParameterList extends ProductParameterSetList
{


    /** @var CategoryProductParameterRepository */
    private $categoryParameterRepo;

    /** @var CategoryProductParameterSaveFacadeFactory */
    private $categoryProductParameterSaveFacadeFactory;

    /** @var CategoryEntity|null */
    private $category;



    public function __construct(CategoryProductParameterRepository $categoryProductParameterRepository,
                                CategoryProductParameterSaveFacadeFactory $categoryProductParameterSaveFacadeFactory,
                                Context $context,
                                GridoFactory $gridoFactory,
                                ParameterStorageFacadeFactory $parameterStorageFacadeFactory,
                                ProductParameterGroupTranslationRepository $productParameterGroupTranslationRepo,
                                ProductParameterRepository $productParameterRepository)
    {
        parent::__construct($context, $gridoFactory, $parameterStorageFacadeFactory, $productParameterGroupTranslationRepo, $productParameterRepository);
        $this->categoryParameterRepo = $categoryProductParameterRepository;
        $this->categoryProductParameterSaveFacadeFactory = $categoryProductParameterSaveFacadeFactory;
    }



    /**
     * @param $category CategoryEntity
     * @return self
     */
    public function setCategory(CategoryEntity $category) : self
    {
        $this->category = $category;
        return $this;
    }



    /**
     * @return CategoryEntity
     * @throws \InvalidArgumentException missing category
     */
    public function getCategory() : CategoryEntity
    {
        if (!$this->category instanceof CategoryEntity) {
            throw new \InvalidArgumentException('Missing category.');
        }
        return $this->category;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $categoryParameter = CategoryProductParameterEntity::getAnnotation();
        $language = $this->localizationResolver->getDefault();
        $parameterTranslation = ProductParameterTranslationEntity::getAnnotation();
        $parameterGroupTranslation = ProductParameterGroupTranslationEntity::getAnnotation();

        $source = new RepositorySource($this->categoryParameterRepo);
        $source->setMethodCount('countJoined');
        $source->setRepositoryMethod('findJoined');
        $source->filter([
            $parameterTranslation->getPropertyByName('languageId')->getColumn()->getName() . ' = ' . $language->getId(),
            $parameterGroupTranslation->getPropertyByName('languageId')->getColumn()->getName() . ' = ' . $language->getId(),
            ['categoryId', '=', $this->getCategory()->getId()],
        ]);
        $source->setDefaultSort($parameterGroupTranslation->getPropertyByName('name')->getColumn()->getName(), 'ASC');

        $grid = $this->getBaseGrid();
        $grid->setModel($source);

        //actions
        $grid->setPrimaryKey($categoryParameter->getPropertyByName('id')->getColumn()->getName());
        $grid->addActionHref('removeParameter', '', $this->getName() . '-removeParameter!')
            ->setIcon('trash')
            ->setCustomRender(function ($row) use ($categoryParameter, $parameterTranslation, $parameterGroupTranslation) {
                $link = $this->link('removeParameter!', ['id' => $row[$categoryParameter->getPropertyByName('id')->getColumn()->getName()]]);
                $parameterName = $row[$parameterTranslation->getPropertyByName('value')->getColumn()->getName()];
                $groupName = $row[$parameterGroupTranslation->getPropertyByName('name')->getColumn()->getName()];
                $confirm = sprintf('Opravdu si přejete smazat parametr \'%s\' ze skupiny \'%s\' ?', $parameterName, $groupName);
                return sprintf('<a href="%s" 
                                   class="grid-action-removeVariant btn btn-default btn-xs btn-mini" 
                                   data-grido-confirm="%s"><i class="fa fa-trash"></i></a>', $link, $confirm);
            });

        return $grid;
    }



    /**
     * Handle for remove parameter.
     * @param $id int id of parameter
     */
    public function handleRemoveParameter(int $id)
    {
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $storageFacade = $this->categoryProductParameterSaveFacadeFactory->create();
            $storageFacade->remove($id);
            $this->database->commit();
            $presenter->flashMessage('Parametr byl smazán.', 'success');
            $presenter->redirect('this');
        } catch (CategoryProductParameterSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }
}