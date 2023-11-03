<?php

namespace App\Components\ArticleForm;

use App\Article\ArticleAggregate;
use App\Article\ArticleEntity;
use App\Article\ArticleFacadeException;
use App\Article\ArticleFacadeFactory;
use App\ArticleCategory\ArticleCategoryRepositoryFactory;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipFacadeFactory;
use App\Components\SeoFormContainer\SeoFormContainer;
use App\Components\SeoFormContainer\SeoFormContainerFactory;
use App\Components\UrlFormContainer\UrlFormContainer;
use App\Components\UrlFormContainer\UrlFormContainerFactory;
use App\Helpers\Entities;
use App\Helpers\Summernote;
use App\Language\LanguageEntity;
use App\Libs\FileManager\Responses\DeleteFileResponse;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleForm extends Control
{


    /** @var Context */
    protected $database;

    /** @var ArticleFacadeFactory */
    protected $articleFacadeFactory;

    /** @var ArticleCategoryRelationshipFacadeFactory */
    protected $articleCategoryRelationshipFacadeFactory;

    /** @var ArticleCategoryRepositoryFactory */
    protected $articleCategoryRepositoryFactory;

    /** @var SeoFormContainerFactory */
    protected $seoFormContainerFactory;

    /** @var UrlFormContainerFactory */
    protected $urlFormContainerFactory;

    /** @var LanguageEntity */
    protected $languageEntity;

    /** @var ArticleAggregate */
    protected $articleAggregate;



    public function __construct(ArticleFacadeFactory $articleFacadeFactory,
                                ArticleCategoryRelationshipFacadeFactory $articleCategoryRelationshipFacadeFactory,
                                ArticleCategoryRepositoryFactory $articleCategoryRepositoryFactory,
                                Context $context,
                                SeoFormContainerFactory $seoFormContainerFactory,
                                UrlFormContainerFactory $urlFormContainerFactory)
    {
        parent::__construct();
        $this->articleFacadeFactory = $articleFacadeFactory;
        $this->articleCategoryRelationshipFacadeFactory = $articleCategoryRelationshipFacadeFactory;
        $this->articleCategoryRepositoryFactory = $articleCategoryRepositoryFactory;
        $this->database = $context;
        $this->seoFormContainerFactory = $seoFormContainerFactory;
        $this->urlFormContainerFactory = $urlFormContainerFactory;
    }



    /**
     * @param LanguageEntity $languageEntity
     * @return $this
     */
    public function setLanguageEntity(LanguageEntity $languageEntity)
    {
        $this->languageEntity = $languageEntity;
        return $this;
    }



    /**
     * @param $aggregate ArticleAggregate
     * @return self
     */
    public function setArticleAggregate(ArticleAggregate $aggregate)
    {
        $this->articleAggregate = $aggregate;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();
        $form->addText("language", "Jazyk")
            ->setAttribute("class", "form-control")
            ->setDisabled(TRUE)
            ->setDefaultValue($this->languageEntity->getName());
        $form->addText("name", "Název")
            ->setAttribute("class", "form-control")
            ->setAttribute('autofocus')
            ->setRequired("Zadejte název článku.");
        $form->addMultiSelect("articleCategory", "Rubriky", $this->getArticleCategoryList())
            ->setAttribute("class", "form-control select2");
        $form->addUpload("coverPhoto", "Úvodní fotografie")
            ->setRequired(FALSE)
            ->addRule(Form::MAX_FILE_SIZE, "Maximální velikost souboru je 10 MB.", 10000000);
        $form->addTextArea("introduction", "Perex", null, 4)
            ->setAttribute("class", "form-control")
            ->setRequired("Vyplňte perex článku.");
        $form->addTextArea("content", "Obsah")
            ->setAttribute("class", "form-control")
            ->setRequired("Vyplňte obsah článku.")
			->setHtmlId('ckEditor')
            ->setEmptyValue(Summernote::EMPTY_STRING_VALUE);
        $form->addSelect("status", "Stav", \App\Helpers\Arrays::toPair(ArticleEntity::getStatuses(), "key", "translate"))
            ->setAttribute("class", "form-control");
        $form->addComponent($this->urlFormContainerFactory->create(), UrlFormContainer::NAME);
        $form->addComponent($this->seoFormContainerFactory->create(), SeoFormContainer::NAME);
        $form->addSubmit("submit", "Uložit")
            ->setAttribute("class", "btn btn-success");
        if (!$this->articleAggregate) {
            $form->onSuccess[] = [$this, "formSuccessNewArticle"];
        } else {
            $article = $this->articleAggregate->getArticleEntity();
            $articleCategories = $this->articleAggregate->getArticleCategories();
            $form->setDefaults([
                "name" => $article->getName(),
                "articleCategory" => $articleCategories ? Entities::getProperty($articleCategories, "id") : null,
                "introduction" => $article->getIntroduction(),
                "content" => $article->getContent(),
                UrlFormContainer::NAME => [
                    "url" => $article->getUrl()
                ],
                "status" => $article->getStatus(),
                SeoFormContainer::NAME => [
                    "titleSeo" => $article->getTitleSeo(),
                    "descriptionSeo" => $article->getDescriptionSeo()
                ]

            ]);
            $form->onSuccess[] = [$this, "formSuccessEditArticle"];
        }
        return $form;
    }



    /**
     * @return array
     */
    protected function getArticleCategoryList()
    {
        $categories = $this->articleCategoryRepositoryFactory->create()
            ->findByLanguageId($this->languageEntity->getId());
        return $categories ? Entities::toPair($categories, "id", "name") : [];
    }



    /**
     * @param $form Form
     * @return void
     */
    public function formSuccessNewArticle(Form $form)
    {
        try {
            $values = $form->getValues();
            $valuesUrl = $values->{UrlFormContainer::NAME};
            $valuesSeo = $values->{SeoFormContainer::NAME};

            $this->database->beginTransaction();
            $article = $this->articleFacadeFactory->create()->add(
                $this->languageEntity->getId(),
                $values->name,
                $values->introduction,
                $values->content,
                $values->status,
                $valuesUrl->url,
                $valuesSeo->titleSeo,
                $valuesSeo->descriptionSeo,
                $values->coverPhoto,
            );

            if ($values->articleCategory) {
                $this->articleCategoryRelationshipFacadeFactory->create()->add($article, $values->articleCategory);
            }
            $this->database->commit();
            $this->presenter->flashMessage("Článek byl uložen.", "success");
            $this->presenter->redirect("Article:edit", ["id" => $article->getId()]);
        } catch (ArticleFacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }
    }



    /**
     * @param Form $form
     */
    public function formSuccessEditArticle(Form $form)
    {
        try {
            $values = $form->getValues();
            $valuesUrl = $values->{UrlFormContainer::NAME};
            $valuesSeo = $values->{SeoFormContainer::NAME};

            $article = $this->articleAggregate->getArticleEntity();
            $article->setName($values->name);
            $article->setUrl($valuesUrl->url);
            $article->setTitleSeo($valuesSeo->titleSeo);
            $article->setDescriptionSeo($valuesSeo->descriptionSeo);
            if ($values->coverPhoto->hasFile()) {
                $article->setCoverPhoto($values->coverPhoto);
            }
            $article->setIntroduction($values->introduction);
            $article->setContent($values->content);
            $article->setStatus($values->status);

            $this->database->beginTransaction();

            $this->articleFacadeFactory->create()->update($article);
            $this->articleCategoryRelationshipFacadeFactory->create()->update($article, $values->articleCategory);

            $this->articleFacadeFactory->create()->update($article);
            $this->database->commit();
            $this->presenter->flashMessage("Článek byl uložen.", "success");
            $this->presenter->redirect("this");
        } catch (ArticleFacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->articleAggregate = $this->articleAggregate;
        $this->template->render();
    }



    /**
     * @return void
     */
    public function handleRemoveCoverPhoto()
    {
        if ($this->presenter->isAjax()) {
            try {
                $this->database->beginTransaction();
                $article = $this->articleAggregate->getArticleEntity();
                $article->setCoverPhoto(NULL);
                $this->articleFacadeFactory->create()->update($article);
                $this->database->commit();
                $response = new DeleteFileResponse('Fotografie byla smazána.', DeleteFileResponse::SUCCESS);
            } catch (ArticleFacadeException $exception) {
                $this->database->rollBack();
                $response = new DeleteFileResponse($exception->getMessage(), DeleteFileResponse::ERROR);
            }
            $this->presenter->sendJson($response->getResponseArray());
        }
    }
}