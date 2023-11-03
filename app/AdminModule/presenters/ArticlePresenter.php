<?php

namespace App\AdminModule\Presenters;

use App\Article\ArticleAggregate;
use App\Article\ArticleAggregateFacadeFactory;
use App\Components\ArticleForm\ArticleForm;
use App\Components\ArticleForm\ArticleFormFactory;
use App\Components\ArticleList\ArticleList;
use App\Components\ArticleList\ArticleListFactory;
use App\Components\ChooseLanguageForm\ChooseLanguageForm;
use App\Components\ChooseLanguageForm\ChooseLanguageFormFactory;
use App\Language\LanguageEntity;
use App\Language\LanguageRepositoryFactory;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticlePresenter extends AdminModulePresenter
{


    /** @var ArticleAggregateFacadeFactory @inject */
    public $articleAggregatorFacadeFactory;

    /** @var ChooseLanguageFormFactory @inject */
    public $languageFormFactory;

    /** @var ArticleListFactory @inject */
    public $articleListFactory;

    /** @var ArticleFormFactory @inject */
    public $articleFormFactory;

    /** @var LanguageRepositoryFactory @inject */
    public $languageRepositoryFactory;

    /** @var LanguageEntity */
    public $languageEntity;

    /** @var ArticleAggregate */
    public $articleAggregate;



    /**
     * @param $langId int
     * @return void
     */
    public function actionAdd($langId = null)
    {
        if (!$langId) {
            $this->template->setFile(__DIR__ . "/templates/Article/templates/preAdd.latte");
        } else {
            $this->languageEntity = $this->checkRequest((int)$langId, LanguageRepositoryFactory::class);
        }
    }



    /**
     * @param $id
     * @throws BadRequestException
     */
    public function actionEdit($id)
    {
        if (!$articleAggregate = $this->articleAggregatorFacadeFactory->create()->create($id, FALSE)) {
            throw new BadRequestException(null, 404);
        }
        $this->articleAggregate = $articleAggregate;
        $this->languageEntity = $this->languageRepositoryFactory->create()->getOneById($this->articleAggregate->getArticleEntity()->getLanguageId());
        $this->template->setFile(__DIR__ . "/templates/Article/add.latte");
    }



    /**
     * @return ArticleForm
     */
    public function createComponentArticleForm()
    {
        $form = $this->articleFormFactory->create();
        $form->setLanguageEntity($this->languageEntity);
        if ($agg = $this->articleAggregate) {
            $form->setArticleAggregate($agg);
        }
        return $form;
    }



    /**
     * @return ChooseLanguageForm
     */
    public function createComponentLanguageForm()
    {
        $form = $this->languageFormFactory->create();
        $form->addOnSuccess(function (Form $form) {
            $values = $form->getValues();
            $this->redirect("Article:add", ["langId" => $values->language]);
        });
        return $form;
    }



    /**
     * @return ArticleList
     */
    public function createComponentArticleList()
    {
        return $this->articleListFactory->create();
    }
}