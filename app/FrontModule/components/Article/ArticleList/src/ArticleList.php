<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Article\ArticleList;

use App\Article\ArticleEntity;
use App\Tests\Article\Article;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ArticleList extends Control
{


    /** @var Article[]|array */
    private $articles = [];



    /**
     * Add article.
     * @param $article ArticleEntity
     * @return self
     */
    public function addArticle(ArticleEntity $article) : self
    {
        $this->articles[] = $article;
        return $this;
    }



    public function render()
    {
        $this->template->articles = $this->articles;
        $this->template->setFile(__DIR__ . '/templates/default.latte');
        $this->template->render();
    }



    public function renderFeatured()
    {
        $this->template->articles = $this->articles;
        $this->template->setFile(__DIR__ . '/templates/featured.latte');
        $this->template->render();
    }



    public function renderList()
    {
        $this->template->articles = $this->articles;
        $this->template->setFile(__DIR__ . '/templates/list.latte');
        $this->template->render();
    }
}