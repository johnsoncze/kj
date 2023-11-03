<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Article\ArticleRelatedList;

use App\Article\ArticleEntity;
use App\Article\ArticleRepository;
use App\FrontModule\Components\Article\ArticleList\ArticleList;
use App\FrontModule\Components\Article\ArticleList\ArticleListFactory;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ArticleRelatedList extends Control
{


    /** @var ArticleEntity|null */
    private $article;

    /** @var ArticleRepository */
    private $articleRepo;

    /** @var ArticleListFactory */
    private $articleListFactory;



    public function __construct(ArticleListFactory $articleListFactory,
                                ArticleRepository $articleRepository)
    {
        parent::__construct();
        $this->articleListFactory = $articleListFactory;
        $this->articleRepo = $articleRepository;
    }



    /**
     * @param $article ArticleEntity
     * @return self
     */
    public function setArticle(ArticleEntity $article): self
    {
        $this->article = $article;
        return $this;
    }



    /**
     * @return ArticleList
     */
    public function createComponentList(): ArticleList
    {
        $articles = $this->getArticles();

        $list = $this->articleListFactory->create();
        foreach ($articles as $article) {
            $list->addArticle($article);
        }
        return $list;
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->articles = $this->getArticles();
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @return ArticleEntity[]|array
     */
    private function getArticles(): array
    {
        static $articles = [];
        static $loaded = FALSE;
        if ($loaded === FALSE) {
            $articles = $this->articleRepo->findPublishedRelatedByArticleId($this->article->getId());
            $loaded = TRUE;
        }
        return $articles;
    }
}