<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Article\CategoryList;

use App\ArticleCategory\ArticleCategoryEntity;
use App\Page\PageEntity;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CategoryList extends Control
{


    /** @var PageEntity */
    private $page;

    /** @var ArticleCategoryEntity[]|array */
    private $categories;



    public function __construct(PageEntity $page, array $categories = [])
    {
        parent::__construct();
        $this->page = $page;
        $this->categories = $categories;
    }



    public function render()
    {
        $this->template->categories = $this->categories;
        $this->template->page = $this->page;

        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}