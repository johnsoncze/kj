<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Category\AssociatedCategory\CategoryForm\CategoryForm AS AssociatedCategoryForm;
use App\AdminModule\Components\Category\AssociatedCategory\CategoryForm\CategoryFormFactory AS AssociatedCategoryFormFactory;
use App\AdminModule\Components\Category\AssociatedCategory\CategoryList\CategoryList AS AssociatedCategoryList;
use App\AdminModule\Components\Category\AssociatedCategory\CategoryList\CategoryListFactory AS AssociatedCategoryListFactory;
use App\AdminModule\Components\Category\CollectionList\ProductForm\ProductForm;
use App\AdminModule\Components\Category\CollectionList\ProductForm\ProductFormFactory;
use App\AdminModule\Components\Category\CollectionList\ProductList\ProductList;
use App\AdminModule\Components\Category\CollectionList\ProductList\ProductListFactory;
use App\AdminModule\Components\Category\CollectionListForm\CollectionListForm;
use App\AdminModule\Components\Category\CollectionListForm\CollectionListFormFactory;
use App\AdminModule\Components\Category\Filtration\Combination\SortForm\SortForm;
use App\AdminModule\Components\Category\Filtration\Combination\SortForm\SortFormFactory;
use App\AdminModule\Components\Category\RepresentativeProduct\PreSortForm\PreSortForm;
use App\AdminModule\Components\Category\RepresentativeProduct\PreSortForm\PreSortFormFactory;
use App\AdminModule\Components\Category\SortForm\Resolver\FirstLevelResolver;
use App\AdminModule\Components\CategoryFiltrationCombinationParameterForm\CategoryFiltrationCombinationParameterForm;
use App\AdminModule\Components\CategoryFiltrationCombinationParameterForm\CategoryFiltrationCombinationParameterFormFactory;
use App\AdminModule\Components\CategoryFiltrationCombinationParameterList\CategoryFiltrationCombinationParameterList;
use App\AdminModule\Components\CategoryFiltrationCombinationParameterList\CategoryFiltrationCombinationParameterListFactory;
use App\AdminModule\Components\CategoryParameterForm\CategoryParameterForm;
use App\AdminModule\Components\CategoryParameterForm\CategoryParameterFormFactory;
use App\AdminModule\Components\CategoryParameterList\CategoryParameterList;
use App\AdminModule\Components\CategoryParameterList\CategoryParameterListFactory;
use App\Category\CategoryEntity;
use App\Category\CategoryRemoveFacadeException;
use App\Category\CategoryRemoveFacadeFactory;
use App\Category\CategoryRepositoryFactory;
use App\Category\Product\Related\Product;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepositoryFactory;
use App\Components\AdminModule\CategoryFiltrationForm\CategoryFiltrationForm;
use App\Components\AdminModule\CategoryFiltrationForm\CategoryFiltrationFormFactory;
use App\Components\AdminModule\CategoryFiltrationList\CategoryFiltrationList;
use App\Components\AdminModule\CategoryFiltrationList\CategoryFiltrationListFactory;
use App\Components\AdminModule\CategoryNavigationTree\CategoryNavigationTree;
use App\Components\AdminModule\CategoryNavigationTree\CategoryNavigationTreeFactory;
use App\Components\CategoryFiltrationSortForm\CategoryFiltrationSortForm;
use App\Components\CategoryFiltrationSortForm\CategoryFiltrationSortFormFactory;
use App\Components\CategoryForm\CategoryForm;
use App\Components\CategoryForm\CategoryFormFactory;
use App\Components\CategoryList\CategoryList;
use App\Components\CategoryList\CategoryListFactory;
use App\Components\ChooseLanguageForm\ChooseLanguageForm;
use App\Components\ChooseLanguageForm\ChooseLanguageFormFactory;
use App\Language\LanguageEntity;
use App\Language\LanguageRepositoryFactory;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryPresenter extends AdminModulePresenter
{


    /** @var AssociatedCategoryFormFactory @inject */
    public $associatedCategoryFormFactory;

    /** @var AssociatedCategoryListFactory @inject */
    public $associatedCategoryListFactory;

    /** @var ChooseLanguageFormFactory @inject */
    public $languageFormFactory;

    /** @var CategoryFiltrationCombinationParameterListFactory @inject */
    public $categoryFiltrationCombinationParameterListFactory;

    /** @var CategoryFiltrationFormFactory @inject */
    public $categoryFiltrationFormFactory;

    /** @var CategoryFormFactory @inject */
    public $categoryFormFactory;

    /** @var CategoryListFactory @inject */
    public $categoryListFactory;

    /** @var CategoryFiltrationCombinationParameterFormFactory @inject */
    public $categoryFiltrationCombinationParameterFormFactory;

    /** @var SortFormFactory @inject */
    public $categoryFiltrationCombinationParameterSortFormFactory;

    /** @var CategoryFiltrationListFactory @inject */
    public $categoryFiltrationListFactory;

    /** @var CategoryFiltrationSortFormFactory @inject */
    public $categoryFiltrationSortFormFactory;

    /** @var CategoryNavigationTreeFactory @inject */
    public $categoryNavigationTreeFactory;

    /** @var CategoryParameterFormFactory @inject */
    public $categoryParameterFormFactory;

    /** @var CategoryParameterListFactory @inject */
    public $categoryParameterListFactory;

    /** @var CategoryRemoveFacadeFactory @inject */
    public $categoryRemoveFacadeFactory;

    /** @var \App\AdminModule\Components\Category\SortForm\SortFormFactory @inject */
    public $categorySortFormFactory;

    /** @var CollectionListFormFactory @inject */
    public $collectionListFormFactory;

    /** @var ProductFormFactory @inject */
    public $collectionListProductFormFactory;

    /** @var ProductListFactory @inject */
    public $collectionListProductListFactory;

    /** @var LanguageEntity|null */
    protected $languageEntity;

    /** @var CategoryEntity|null */
    protected $categoryEntity;

    /** @var CategoryFiltrationGroupEntity|null */
    protected $categoryFiltrationGroupEntity;

    /** @var PreSortFormFactory @inject */
    public $preRepresentativeProductSortFormFactory;

    /** @var \App\AdminModule\Components\Category\RepresentativeProduct\SortForm\SortFormFactory @inject */
    public $representativeProductSortFormFactory;



    /**
     * @inheritdoc
     */
    public function beforeRender()
    {
        parent::beforeRender();
        if ($this->categoryEntity instanceof CategoryEntity) {
            $this->addToHeadline($this->categoryEntity->getName());
        }
    }



    /**
     * @param $langId int
     * @return void
     */
    public function actionAdd($langId = NULL)
    {
        if (!$langId) {
            $this->template->setFile(__DIR__ . "/templates/Category/templates/preAdd.latte");
        } else {
            $this->languageEntity = $this->checkRequest((int)$langId, LanguageRepositoryFactory::class);
        }
    }



    /**
     * @param int $id
     */
    public function actionEdit(int $id)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->languageEntity = $this->checkRequest($this->categoryEntity->getLanguageId(), LanguageRepositoryFactory::class);

        $this->template->setFile(__DIR__ . "/templates/Category/add.latte");
        $this->template->category = $this->categoryEntity;
    }



    /**
     * @param $id int
     * @return void
     * @throws \Exception
     */
    public function actionEditAssociated(int $id)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->languageEntity = $this->checkRequest($this->categoryEntity->getLanguageId(), LanguageRepositoryFactory::class);

        $this->template->setFile(__DIR__ . '/templates/Category/editAssociated.latte');
        $this->template->category = $this->categoryEntity;
    }



    /**
     * @param $id int
     * @return void
     * @throws \Exception
     */
    public function actionEditAssociatedProduct(int $id)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->languageEntity = $this->checkRequest($this->categoryEntity->getLanguageId(), LanguageRepositoryFactory::class);

        $this->template->category = $this->categoryEntity;
    }



    /**
     * @param $languageId int|null
     * @param $categoryParentId int|string|null
     * @return BadRequestException
     */
    public function actionSort(int $languageId = NULL, $categoryParentId = NULL)
    {
        if ($languageId === NULL) {
            $this->template->setFile(__DIR__ . '/templates/Category/templates/preSort.latte');
        } else {
            $this->languageEntity = $this->checkRequest($languageId, LanguageRepositoryFactory::class);
        }
    }



    /**
     * "editFiltration" action
     * @param $id int category id
     */
    public function actionEditFiltration(int $id)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Category:edit', ['id' => $id]), $this->translator->translate('presenter.admin.category.edit'), 1);

        $this->template->category = $this->categoryEntity;
    }



    /**
     * "editParameter" action.
     * @param $id int
     * @return void
     */
    public function actionEditParameter(int $id)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Category:edit', ['id' => $id]), $this->translator->translate('presenter.admin.category.edit'), 1);

        $this->template->category = $this->categoryEntity;
    }



    /**
     * "editFiltrationCombination" action
     *
     * @param $id int category id
     */
    public function actionEditFiltrationCombination(int $id)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Category:edit', ['id' => $id]), $this->translator->translate('presenter.admin.category.edit'), 1);

        $this->template->category = $this->categoryEntity;
    }



    /**
     * "editFiltrationCombinationForm" action.
     * @param $id int category id
     */
    public function actionEditFiltrationCombinationAdd(int $id)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Category:edit', ['id' => $id]), $this->translator->translate('presenter.admin.category.edit'), 1);
        $this->_navigation->addLink($this->link('Category:editFiltrationCombination', ['id' => $id]), $this->translator->translate('presenter.admin.category.editfiltrationcombination'), 2);

        $this->template->category = $this->categoryEntity;
    }



    /**
     * "editFiltrationCombinationForm" action.
     * @param $id int category id
     * @param $combinationId int|null
     */
    public function actionEditFiltrationCombinationEdit(int $id, int $combinationId = NULL)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->categoryFiltrationGroupEntity = $this->checkRequest((int)$this->getParameter('combinationId'), CategoryFiltrationGroupRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Category:edit', ['id' => $id]), $this->translator->translate('presenter.admin.category.edit'), 1);
        $this->_navigation->addLink($this->link('Category:editFiltrationCombination', ['id' => $id]), $this->translator->translate('presenter.admin.category.editfiltrationcombination'), 2);

        $this->template->category = $this->categoryEntity;
        $this->template->setFile(__DIR__ . '/templates/Category/editFiltrationCombinationAdd.latte');
    }



    /**
     * @param $id int
     */
    public function actionCollectionList(int $id)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Category:edit', ['id' => $id]), $this->translator->translate('presenter.admin.category.edit'), 1);

        $this->template->category = $this->categoryEntity;
    }



    /**
     * @param $id int
     * @param $type string|null
     * @throws InvalidLinkException
     * @throws BadRequestException
     * @throws \Exception
     */
    public function actionRepresentativeProductSort(int $id, string $type = NULL)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Category:editAssociatedProduct', ['id' => $id]), $this->translator->translate('presenter.admin.category.editassociatedproduct'), 1);

        try {
            if ($type === NULL) {
                $this->template->setFile(__DIR__ . '/templates/Category/templates/preRepresentativeProductSort.latte');
            } else {
                $typeValues = Product::getTypeValue($type);
            }

            $this->template->category = $this->categoryEntity;
            $this->template->type = $typeValues ?? [];
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
     * "sortFiltrationCombination" action.
     * @param $id int
     * @return void
     */
    public function actionSortFiltrationCombination(int $id)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Category:editFiltrationCombination', ['id' => $id]), $this->translator->translate('presenter.admin.category.editfiltrationcombination'), 1);

        $this->template->category = $this->categoryEntity;
    }



    /**
     * "filtrationSort" action
     * @param $id int
     */
    public function actionFiltrationSort(int $id)
    {
        $this->categoryEntity = $this->checkRequest($id, CategoryRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Category:editFiltration', ['id' => $id]), $this->translator->translate('presenter.admin.category.editfiltration'), 1);

        $this->template->category = $this->categoryEntity;
    }



    /**
     * @return AssociatedCategoryForm
    */
    public function createComponentAssociatedCategoryForm() : AssociatedCategoryForm
    {
        $form = $this->associatedCategoryFormFactory->create();
        $form->setCategory($this->categoryEntity);

        return $form;
    }



    /**
     * @return AssociatedCategoryList
     */
    public function createComponentAssociatedCategoryList() : AssociatedCategoryList
    {
        $list = $this->associatedCategoryListFactory->create();
        $list->setCategory($this->categoryEntity);

        return $list;
    }



    /**
     * @return ChooseLanguageForm
     * @throws AbortException
     */
    public function createComponentLanguageForm() : ChooseLanguageForm
    {
        $form = $this->languageFormFactory->create();
        $form->addOnSuccess(function (Form $form) {
            $values = $form->getValues();
            $this->redirect("Category:add", ["langId" => $values->language]);
        });
        return $form;
    }



    /**
     * @return CategoryForm
     */
    public function createComponentCategoryForm() : CategoryForm
    {
        $form = $this->categoryFormFactory->create();
        $form->setLanguageEntity($this->languageEntity);
        if ($this->categoryEntity instanceof CategoryEntity) {
            $form->setCategoryEntity($this->categoryEntity);
        }
        return $form;
    }



    /**
     * @return CategoryList
     */
    public function createComponentCategoryList() : CategoryList
    {
        return $this->categoryListFactory->create();
    }



    /**
     * @return CategoryNavigationTree
     */
    public function createComponentCategoryNavigationTree() : CategoryNavigationTree
    {
        return $this->categoryNavigationTreeFactory->create();
    }



    /**
     * @return CategoryFiltrationList
     */
    public function createComponentCategoryFiltrationList() : CategoryFiltrationList
    {
        $list = $this->categoryFiltrationListFactory->create();
        $list->setCategoryEntity($this->categoryEntity);
        return $list;
    }



    /**
     * @return CategoryFiltrationCombinationParameterList
     */
    public function createComponentCombinationParameterList() : CategoryFiltrationCombinationParameterList
    {
        $list = $this->categoryFiltrationCombinationParameterListFactory->create();
        $list->setCategoryEntity($this->categoryEntity);
        return $list;
    }



    /**
     * @return CategoryFiltrationForm
     */
    public function createComponentCategoryFiltrationForm() : CategoryFiltrationForm
    {
        $form = $this->categoryFiltrationFormFactory->create();
        $form->setCategoryEntity($this->categoryEntity);
        return $form;
    }



    /**
     * @return CategoryFiltrationSortForm
     */
    public function createComponentCategoryFiltrationSortForm() : CategoryFiltrationSortForm
    {
        $form = $this->categoryFiltrationSortFormFactory->create();
        $form->setCategoryEntity($this->categoryEntity);
        return $form;
    }



    /**
     * @return CategoryParameterForm
     */
    public function createComponentCategoryParameterForm() : CategoryParameterForm
    {
        $form = $this->categoryParameterFormFactory->create();
        $form->setCategory($this->categoryEntity);
        return $form;
    }



    /**
     * @return CategoryParameterList
     */
    public function createComponentCategoryParameterList() : CategoryParameterList
    {
        $form = $this->categoryParameterListFactory->create();
        $form->setCategory($this->categoryEntity);
        return $form;
    }



    /**
     * @return \App\AdminModule\Components\Category\SortForm\SortForm
     */
    public function createComponentCategorySortForm() : \App\AdminModule\Components\Category\SortForm\SortForm
    {
        $form = $this->categorySortFormFactory->create();
        $form->setLanguage($this->languageEntity);
        return $form;
    }



    /**
     * @return CollectionListForm
     */
    public function createComponentCollectionListForm() : CollectionListForm
    {
        $form = $this->collectionListFormFactory->create();
        $form->setCategory($this->categoryEntity);
        return $form;
    }



    /**
     * @return ProductForm
     */
    public function createComponentCollectionListProductForm() : ProductForm
    {
        $form = $this->collectionListProductFormFactory->create();
        $form->setCategory($this->categoryEntity);
        return $form;
    }



    /**
     * @return ProductList
     */
    public function createComponentCollectionListProductList() : ProductList
    {
        $list = $this->collectionListProductListFactory->create();
        $list->setCategory($this->categoryEntity);
        return $list;
    }



    /**
     * @return CategoryFiltrationCombinationParameterForm
     */
    public function createComponentCombinationParameterForm() : CategoryFiltrationCombinationParameterForm
    {
        $form = $this->categoryFiltrationCombinationParameterFormFactory->create();
        $form->setCategoryEntity($this->categoryEntity);
        if ($this->categoryFiltrationGroupEntity instanceof CategoryFiltrationGroupEntity) {
            $form->setCategoryFiltrationGroupEntity($this->categoryFiltrationGroupEntity);
        }
        return $form;
    }



    /**
     * @return SortForm
     */
    public function createComponentCombinationParameterSortForm() : SortForm
    {
        $form = $this->categoryFiltrationCombinationParameterSortFormFactory->create();
        $form->setCategory($this->categoryEntity);
        return $form;
    }



    /**
     * @return PreSortForm
     */
    public function createComponentPreRepresentativeProductSortForm() : PreSortForm
    {
        return $this->preRepresentativeProductSortFormFactory->create();
    }



    /**
     * @return \App\AdminModule\Components\Category\RepresentativeProduct\SortForm\SortForm
     */
    public function createComponentRepresentativeProductSortForm() : \App\AdminModule\Components\Category\RepresentativeProduct\SortForm\SortForm
    {
        $form = $this->representativeProductSortFormFactory->create();
        $form->setCategory($this->categoryEntity);
        $form->setType($this->getParameter(PreSortForm::PARAMETER_TYPE));
        return $form;
    }



    /**
     * @return ChooseLanguageForm
     * @throws AbortException
     */
    public function createComponentSortLanguageForm() : ChooseLanguageForm
    {
        $form = $this->languageFormFactory->create();
        $form->addOnSuccess(function (Form $form) {
            $values = $form->getValues();
            $params['languageId'] = $values->language;
            $params[\App\AdminModule\Components\Category\SortForm\SortForm::CATEGORY_PARENT_ID] = FirstLevelResolver::KEY;
            $this->redirect("Category:sort", $params);
        });
        return $form;
    }



    /**
     * @param int $id
     */
    public function handleRemove(int $id)
    {
        try {
            $this->database->beginTransaction();
            $facade = $this->categoryRemoveFacadeFactory->create();
            $facade->remove($id);
            $this->database->commit();

            $this->flashMessage("Kategorie byla smazána.", "success");
            $this->redirect("this");
        } catch (CategoryRemoveFacadeException $exception) {
            $this->database->rollBack();
            $this->flashMessage($exception->getMessage(), "danger");
        }
    }


}