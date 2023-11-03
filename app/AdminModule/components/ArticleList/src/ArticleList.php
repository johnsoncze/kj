<?php

namespace App\Components\ArticleList;

use App\Article\ArticleEntity;
use App\Article\ArticleFacadeException;
use App\Article\ArticleFacadeFactory;
use App\Article\ArticleRepositoryFactory;
use App\ArticleCategory\ArticleCategoryEntity;
use App\ArticleCategory\ArticleCategoryRepositoryFactory;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipEntity;
use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Helpers\Entities;
use App\Language\LanguageEntity;
use App\Language\LanguageRepositoryFactory;
use Grido\Grid;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleList extends GridoComponent
{


    /** @var ArticleFacadeFactory */
    protected $articleFacadeFactory;

    /** @var ArticleCategoryRepositoryFactory */
    protected $articleCategoryRepositoryFactory;

    /** @var ArticleRepositoryFactory */
    protected $articleRepositoryFactory;

    /** @var LanguageRepositoryFactory */
    protected $languageRepositoryFactory;

    /** @var Context */
    protected $database;



    public function __construct(GridoFactory $gridoFactory,
                                ArticleRepositoryFactory $articleRepositoryFactory,
                                LanguageRepositoryFactory $languageRepositoryFactory,
                                ArticleFacadeFactory $articleFacadeFactory,
                                ArticleCategoryRepositoryFactory $articleCategoryRepositoryFactory,
                                Context $context)
    {
        parent::__construct($gridoFactory);
        $this->articleRepositoryFactory = $articleRepositoryFactory;
        $this->articleCategoryRepositoryFactory = $articleCategoryRepositoryFactory;
        $this->languageRepositoryFactory = $languageRepositoryFactory;
        $this->articleFacadeFactory = $articleFacadeFactory;
        $this->database = $context;
    }



    /**
     * @return \Grido\Grid
     */
    public function createComponentList()
    {
        $languageRepository = $this->languageRepositoryFactory->create();
        $languages = $languageRepository->findAll();

        $articleCategoryRepository = $this->articleCategoryRepositoryFactory->create();
        $articleCategories = $articleCategoryRepository->findBy(["sort" => ["sort", "ASC"]]);
        $categoryList = $articleCategories && $languages ? $this->getCategoryList($articleCategories, $languages) : [];

        $languageList = Entities::toPair($languages, "id", "name");
        $statusList = Arrays::toPair(ArticleEntity::getStatuses(), "key", "translate");

        //Annotation of ArticleCategoryRelationEntity
        $articleCategoryRelationshipAnnotation = ArticleCategoryRelationshipEntity::getAnnotation();
        $acrTable = $articleCategoryRelationshipAnnotation->getTable()->getName();
        $articleCategoryIdProperty = $articleCategoryRelationshipAnnotation->getPropertyByName("articleCategoryId");

        $source = new RepositorySource($this->articleRepositoryFactory->create());
        $source->setDefaultSort("id", "DESC");

        $grido = $this->gridoFactory->create();
        $grido->setModel($source);

        $grido->addColumnText("name", "Název")
            ->setSortable()
            ->setFilterText();
        $grido->addColumnText("category", "Rubriky")
            ->setColumn(":{$acrTable}.{$articleCategoryIdProperty->getColumn()->getName()}")
            ->setCustomRender(function ($row) use ($articleCategories) {
                if ($row["categories"]) {
                    $text = "";
                    foreach ($row["categories"] as $category) {
                        $text .= $articleCategories[$category["articleCategoryId"]]->getName() . "<br>";
                    }
                    return $text;
                }
                return "-";
            })
            ->setFilterSelect(Arrays::mergeTree(["" => ""], $categoryList));
        $grido->addColumnText("languageId", "Jazyk")
            ->setSortable()
            ->setReplacement($languageList)
            ->setFilterSelect(Arrays::mergeTree(["" => ""], $languageList))
            ->setDefaultValue("");
        $grido->addColumnText("status", "Stav")
            ->setReplacement($statusList)
            ->setSortable()
            ->setFilterSelect(Arrays::mergeTree(["" => ""], $statusList));
        $grido->addColumnDate("addDate", "Datum přidání")
            ->setDateFormat("d.m.Y H:i:s")
            ->setSortable();

        $grido->addActionHref("edit", null, "Article:edit")
            ->setIcon("pencil");
        $grido->addActionHref("remove", null, "articleList:articleRemove!")
            ->setIcon("trash")
            ->setConfirm(function ($row) {
                return "Opravdu si přejete smazat článek '" . $row["name"] . "'?";
            });

        $grido->getColumn("name")->getHeaderPrototype()->style["width"] = "30%";
        $grido->getColumn("category")->getHeaderPrototype()->style["width"] = "15%";
        $grido->getColumn("languageId")->getHeaderPrototype()->style["width"] = "15%";
        $grido->getColumn("status")->getHeaderPrototype()->style["width"] = "15%";
        $grido->getColumn("addDate")->getHeaderPrototype()->style["width"] = "15%";

        return $grido;
    }



    /**
     * @param $entities ArticleCategoryEntity[]
     * @param $languages LanguageEntity[]
     * @return array [Language => [items..],..]
     */
    protected function getCategoryList(array $entities, array $languages)
    {
        $list = [];
        foreach ($entities as $entity) {
            $list[$languages[$entity->getLanguageId()]->getName()][$entity->getId()] = $entity->getName();
        }
        return $list;
    }



    /**
     * @param $id int
     */
    public function handleArticleRemove($id)
    {
        try {
            $this->database->beginTransaction();
            $this->articleFacadeFactory->create()->remove($id);
            $this->database->commit();
            $this->presenter->flashMessage("Článek byl odstraněn.", "success");
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