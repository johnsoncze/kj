<?php

namespace App\Components\PromoArticleList;

use App\PromoArticle\PromoArticleEntity;
use App\PromoArticle\PromoArticleFacadeException;
use App\PromoArticle\PromoArticleFacadeFactory;
use App\PromoArticle\PromoArticleRepositoryFactory;
use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Helpers\Entities;
use Grido\Grid;
use Nette\Database\Context;



class PromoArticleList extends GridoComponent
{
    /** @var PromoArticleFacadeFactory */
    protected $promoArticleFacadeFactory;
		
    /** @var PromoArticleRepositoryFactory */
    protected $promoArticleRepositoryFactory;

    /** @var Context */
    protected $database;



    public function __construct(GridoFactory $gridoFactory,
                                PromoArticleRepositoryFactory $promoArticleRepositoryFactory,
                                PromoArticleFacadeFactory $promoArticleFacadeFactory,
                                Context $context)
    {
        parent::__construct($gridoFactory);
        $this->promoArticleRepositoryFactory = $promoArticleRepositoryFactory;
        $this->promoArticleFacadeFactory = $promoArticleFacadeFactory;
        $this->database = $context;
    }



    /**
     * @return \Grido\Grid
     */
    public function createComponentList()
    {
			/*
        $articleCategoryRepository = $this->articleCategoryRepositoryFactory->create();
        $articleCategories = $articleCategoryRepository->findBy(["sort" => ["sort", "ASC"]]);
        $categoryList = $articleCategories && $languages ? $this->getCategoryList($articleCategories, $languages) : [];

        $languageList = Entities::toPair($languages, "id", "name");
        $statusList = Arrays::toPair(ArticleEntity::getStatuses(), "key", "translate");

        $articleCategoryRelationshipAnnotation = ArticleCategoryRelationshipEntity::getAnnotation();
        $acrTable = $articleCategoryRelationshipAnnotation->getTable()->getName();
        $articleCategoryIdProperty = $articleCategoryRelationshipAnnotation->getPropertyByName("articleCategoryId");
				*/

        $source = new RepositorySource($this->promoArticleRepositoryFactory->create());
        $source->setDefaultSort("id", "DESC");

        $grido = $this->gridoFactory->create();
        $grido->setModel($source);

        $grido->addColumnText("title", "Název")
            ->setSortable()
            ->setFilterText();
        $grido->addColumnText("isDefault", "Defaultní")
            ->setSortable()
            ->setFilterText();
        $grido->addColumnText("sequence", "Pořadí")
            ->setSortable()
            ->setFilterText();
        $grido->addActionHref("edit", null, "PromoArticle:edit")
            ->setIcon("pencil");
        $grido->addActionHref("remove", null, "promoArticleList:promoArticleRemove!")
            ->setIcon("trash")
            ->setConfirm(function ($row) {
                return "Opravdu si přejete smazat promo článek '" . $row["title"] . "'?";
            });
						
        $grido->getColumn("title")->getHeaderPrototype()->style["width"] = "30%";
        $grido->getColumn("sequence")->getHeaderPrototype()->style["width"] = "15%";
        $grido->getColumn("isDefault")->getHeaderPrototype()->style["width"] = "15%";
        return $grido;
    }


    /**
     * @param $id int
     */
    public function handlePromoArticleRemove($id)
    {
        try {
            $this->database->beginTransaction();
            $this->promoArticleFacadeFactory->create()->remove($id);
            $this->database->commit();
            $this->presenter->flashMessage("Promo článek byl odstraněn.", "success");
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
        $this->template->render();
    }
}