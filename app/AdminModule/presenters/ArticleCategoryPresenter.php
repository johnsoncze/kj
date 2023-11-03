<?php

namespace App\AdminModule\Presenters;

use App\Article\Module\Module;
use App\Article\Module\ModuleRepository;
use App\ArticleCategory\ArticleCategoryEntity;
use App\ArticleCategory\ArticleCategoryFacadeFactory;
use App\ArticleCategory\ArticleCategoryRepositoryFactory;
use App\Components\ArticleCategoryForm\ArticleCategoryForm;
use App\Components\ArticleCategoryForm\ArticleCategoryFormFactory;
use App\Components\ArticleCategoryList\ArticleCategoryList;
use App\Components\ArticleCategoryList\ArticleCategoryListFactory;
use App\Components\ArticleCategoryRemoveForm\ArticleCategoryRemoveForm;
use App\Components\ArticleCategoryRemoveForm\ArticleCategoryRemoveFormFactory;
use App\Components\ChooseLanguageForm\ChooseLanguageForm;
use App\Components\ChooseLanguageForm\ChooseLanguageFormFactory;
use App\Components\SortForm\SortForm;
use App\Components\SortForm\SortFormFactory;
use App\FacadeException;
use App\Helpers\Entities;
use App\Language\LanguageEntity;
use App\Language\LanguageFacadeFactory;
use App\Language\LanguageRepositoryFactory;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @breadcrumb-nav-parent :Admin:Article:default
 */
class ArticleCategoryPresenter extends AdminModulePresenter
{


    /** @var ChooseLanguageFormFactory @inject */
    public $chooseLanguageFormFactory;

    /** @var SortFormFactory @inject */
    public $sortFormFactory;

    /** @var ArticleCategoryFormFactory @inject */
    public $articleCategoryFormFactory;

    /** @var ArticleCategoryRemoveFormFactory @inject */
    public $articleCategoryRemoveFormFactory;

    /** @var ArticleCategoryListFactory @inject */
    public $articleCategoryListFactory;

    /** @var ModuleRepository @inject */
    public $articleModuleRepo;

    /** @var LanguageFacadeFactory @inject */
    public $languageFacadeFactory;

    /** @var LanguageRepositoryFactory @inject */
    public $languageRepositoryFactory;

    /** @var ArticleCategoryRepositoryFactory @inject */
    public $articleCategoryRepositoryFactory;

    /** @var ArticleCategoryFacadeFactory @inject */
    public $articleCategoryFacadeFactory;

    /** @var LanguageEntity */
    public $languageEntity;

    /** @var ArticleCategoryEntity */
    public $articleCategoryEntity;

    /** @var Module|null */
    public $articleModule;



    /**
     * @param $langId int
     * @return void
     */
    public function actionAdd($langId = null)
    {
        if (!$langId) {
            $this->template->setFile(__DIR__ . "/templates/ArticleCategory/templates/preAdd.latte");
        } else {
            $this->languageEntity = $this->checkRequest((int)$langId, LanguageRepositoryFactory::class);
        }
    }



    /**
     * @param $id int
     * @return void
     */
    public function actionEdit($id)
    {
        $this->articleCategoryEntity = $this->checkRequest((int)$id, ArticleCategoryRepositoryFactory::class);
        $this->languageEntity = $this->languageRepositoryFactory->create()->getOneById($this->articleCategoryEntity->getLanguageId());
        $this->template->setFile(__DIR__ . "/templates/ArticleCategory/add.latte");
    }



    /**
     * @param $id int
     * @return void
     */
    public function actionRemove($id)
    {
        $this->articleCategoryEntity = $this->checkRequest((int)$id, ArticleCategoryRepositoryFactory::class);
    }



    /**
     * @param $langId int|null
     * @param $moduleId int|null
     */
    public function actionSort(int $langId = null, int $moduleId = NULL)
    {
        if (!$langId) {
            $this->template->setFile(__DIR__ . "/templates/ArticleCategory/templates/preSort.latte");
        } else {
            $this->template->languageEntity = $this->languageEntity = $this->checkRequest((int)$langId, LanguageRepositoryFactory::class);
            $this->template->module = $this->articleModule = $this->articleModuleRepo->getOneById($moduleId);
        }
    }



    /**
     * @return ChooseLanguageForm
     */
    public function createComponentLanguageForm()
    {
        $form = $this->chooseLanguageFormFactory->create();
        $form->addOnSuccess(function (Form $form) {
            $values = $form->getValues();
            $this->redirect("ArticleCategory:add", ["langId" => $values->language]);
        });
        return $form;
    }



    /**
     * @return ArticleCategoryForm
     */
    public function createComponentArticleCategoryForm()
    {
        $form = $this->articleCategoryFormFactory->create();
        $form->setLanguageEntity($this->languageEntity);
        if ($this->articleCategoryEntity) {
            $form->setArticleCategoryEntity($this->articleCategoryEntity);
        }
        return $form;
    }



    /**
     * @return ArticleCategoryRemoveForm
     */
    public function createComponentArticleCategoryRemoveForm()
    {
        $form = $this->articleCategoryRemoveFormFactory->create();
        $form->setArticleCategoryEntity($this->articleCategoryEntity);
        return $form;
    }



    /**
     * @return ArticleCategoryList
     */
    public function createComponentArticleCategoryList()
    {
        return $this->articleCategoryListFactory->create();
    }



    /**
     * @return ChooseLanguageForm
     */
    public function createComponentLanguageFormForSort()
    {
        $moduleList = $this->articleModuleRepo->findAll();
        $moduleList = $moduleList ? Entities::toPair($moduleList, 'id', 'name') : [];

        $form = $this->chooseLanguageFormFactory->create();
        $form->createFormCallback(function (Form $form) use ($moduleList) {
            $form->addSelect('module', 'Modul*')
                ->setPrompt('- vyberte -')
                ->setRequired('Vyberte modul.')
                ->setAttribute('class', 'form-control')
                ->setItems($moduleList);
        });
        $form->addOnSuccess(function (Form $form) {
            $values = $form->getValues();
            $this->redirect("ArticleCategory:sort", ["langId" => $values->language, 'moduleId' => $values->module]);
        });
        return $form;
    }



    /**
     * @return SortForm
     */
    public function createComponentSortForm()
    {
        //Items
        $articleCategories = $this->articleCategoryRepositoryFactory->create()->findByLanguageIdAndModuleId($this->languageEntity->getId(), $this->articleModule->getId());
        $items = Entities::toPair($articleCategories, "id", "name");

        $form = $this->sortFormFactory->create();
        $form->setItems($items ? $items : []);
        $form->setOnSuccess(function (Form $form, array $data) use ($articleCategories) {
            try {
                $this->database->beginTransaction();
                $this->articleCategoryFacadeFactory->create()->sort($articleCategories, $data);
                $this->database->commit();
                $this->flashMessage("Řazení bylo uloženo.", "success");
                $this->redirect("this");
            } catch (FacadeException $exception) {
                $this->database->rollBack();
                $this->flashMessage($exception->getMessage(), "danger");
            }
        });
        return $form;
    }
}