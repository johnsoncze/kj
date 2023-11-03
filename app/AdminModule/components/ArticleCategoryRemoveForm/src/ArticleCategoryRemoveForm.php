<?php

namespace App\Components\ArticleCategoryRemoveForm;

use App\ArticleCategory\ArticleCategoryEntity;
use App\ArticleCategory\ArticleCategoryFacadeFactory;
use App\ArticleCategory\ArticleCategoryRepositoryFactory;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipFacadeFactory;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipRepositoryFactory;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;
use App\FacadeException;
use App\Helpers\Entities;
use App\Language\LanguageRepositoryFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCategoryRemoveForm extends Control
{


    /** @var Context */
    protected $database;

    /** @var ArticleCategoryFacadeFactory */
    protected $articleCategoryFacadeFactory;

    /** @var ArticleCategoryRelationshipFacadeFactory */
    protected $articleCategoryRelationshipFacadeFactory;

    /** @var ArticleCategoryRepositoryFactory */
    protected $articleCategoryRepositoryFactory;

    /** @var ArticleCategoryRelationshipRepositoryFactory */
    protected $articleCategoryRelationshipRepositoryFactory;

    /** @var LanguageRepositoryFactory */
    protected $languageRepositoryFactory;

    /** @var ArticleCategoryEntity */
    protected $articleCategoryEntity;

    /** @var CountDTO */
    protected $articlesCount;

    /** @var array */
    protected $articleCategoryList = [];



    public function __construct(ArticleCategoryFacadeFactory $articleCategoryFacadeFactory,
                                ArticleCategoryRelationshipRepositoryFactory $articleCategoryRelationshipRepositoryFactory,
                                ArticleCategoryRepositoryFactory $articleCategoryRepositoryFactory,
                                Context $context,
                                ArticleCategoryRelationshipFacadeFactory $articleCategoryRelationshipFacadeFactory,
                                LanguageRepositoryFactory $languageRepositoryFactory)
    {
        $this->articleCategoryFacadeFactory = $articleCategoryFacadeFactory;
        $this->articleCategoryRelationshipRepositoryFactory = $articleCategoryRelationshipRepositoryFactory;
        $this->articleCategoryRepositoryFactory = $articleCategoryRepositoryFactory;
        $this->database = $context;
        $this->articleCategoryRelationshipFacadeFactory = $articleCategoryRelationshipFacadeFactory;
        $this->languageRepositoryFactory = $languageRepositoryFactory;
    }



    /**
     * @param $articleCategoryEntity ArticleCategoryEntity
     * @return self
     */
    public function setArticleCategoryEntity(ArticleCategoryEntity $articleCategoryEntity)
    {
        $this->articleCategoryEntity = $articleCategoryEntity;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();
        $form->addSelect("articleCategory", "Náhradní rubrika", $this->getArticleCategoryList())
            ->setPrompt("- Vyberte -")
            ->setAttribute("class", "form-control");
        $form->addSubmit("submit", "Odstranit")
            ->setAttribute("class", "btn btn-success");
        $form->onSuccess[] = [$this, "formSuccess"];
        return $form;
    }



    /**
     * @return void
     */
    public function formSuccess(Form $form)
    {
        try {
            $values = $form->getValues();
            $this->database->beginTransaction();

            //Move articles to another category
            if ($values->articleCategory) {
                $count = $this->articleCategoryRelationshipFacadeFactory
                    ->create()
                    ->replaceCategory($this->articleCategoryEntity->getId(), $values->articleCategory);
            }

            $this->articleCategoryFacadeFactory->create()->remove($this->articleCategoryEntity);
            $this->database->commit();
            $this->presenter->flashMessage("Rubrika byla smazána.", "success");
            if (isset($count)) {
                $this->presenter->flashMessage("Bylo přesunuto celkem {$count} článků do kategorie '{$this->getArticleCategoryList()[$values->articleCategory]}'.", "success");
            }
            $this->presenter->redirect("ArticleCategory:default");
        } catch (FacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage());
        }
    }



    /**
     * @return array
     */
    protected function getArticleCategoryList()
    {
        if (!$this->articleCategoryList
            && $this->getArticlesCount()->getCount() > 0
            && ($categories = $this->articleCategoryRepositoryFactory->create()->findBy([
                "where" => [
                    ["id", "!=", $this->articleCategoryEntity->getId()],
                    ["languageId", "=", $this->articleCategoryEntity->getLanguageId()]
                ]
            ]))
        ) {
            return Entities::toPair($categories, "id", "name");
        }
        return $this->articleCategoryList;
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->articleCategoryEntity = $this->articleCategoryEntity;
        $this->template->articlesCount = $this->getArticlesCount();
        $this->template->languageEntity = $this->languageRepositoryFactory->create()->getOneById($this->articleCategoryEntity->getLanguageId());
        $this->template->render();
    }



    /**
     * @return CountDTO
     */
    protected function getArticlesCount()
    {
        if (!$this->articlesCount) {
            $result = $this->articleCategoryRelationshipRepositoryFactory->create()
                ->getCategoryArticlesCount($this->articleCategoryEntity->getId());
            $this->articlesCount = $result;
        }
        return $this->articlesCount;
    }
}