<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Article\ArticleEntity;
use App\Article\ArticleFacadeException;
use App\Article\ArticleFacadeFactory;
use App\Article\ArticleRepository;
use App\Article\Module\Module;
use App\Article\Module\ModuleRepository;
use App\ArticleCategory\ArticleCategoryEntity;
use App\ArticleCategory\ArticleCategoryRepository;
use App\FrontModule\Components\Article\ArticleList\ArticleList;
use App\FrontModule\Components\Article\ArticleList\ArticleListFactory;
use App\FrontModule\Components\Article\ArticleRelatedList\ArticleRelatedList;
use App\FrontModule\Components\Article\ArticleRelatedList\ArticleRelatedListFactory;
use App\FrontModule\Components\Article\CategoryList\CategoryList;
use App\FrontModule\Components\Article\CategoryList\CategoryListFactory;
use App\FrontModule\Components\Breadcrumb\Item;
use App\FrontModule\Components\Pagination\Pagination;
use App\FrontModule\Components\Pagination\PaginationFactory;
use App\Helpers\Entities;
use App\NotFoundException;
use App\Page\PageEntity;
use App\Page\PageRepository;
use Nette\Application\BadRequestException;
use Nette\Application\LinkGenerator;
use Nette\Utils\Paginator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ArticlePresenter extends AbstractPresenter
{

    /** @var ArticleEntity|null */
    public $article;

    /** @var ArticleEntity[]|array */
    public $articles = [];

    /** @var ArticleCategoryEntity[]|array */
    public $articleCategories = [];

    /** @var ArticleCategoryEntity|null */
    public $articleCategory;

    /** @var CategoryListFactory @inject */
    public $articleCategoryListFactory;

    /** @var ArticleCategoryRepository @inject */
    public $articleCategoryRepo;

    /** @var ArticleFacadeFactory @inject */
    public $articleFacadeFactory;

    /** @var ArticleListFactory @inject */
    public $articleListFactory;

    /** @var ArticleRelatedListFactory @inject */
    public $articleRelatedListFactory;

    /** @var ArticleRepository @inject */
    public $articleRepo;

    /** @var Module|null */
    public $module;

    /** @var ModuleRepository @inject */
    public $moduleRepo;

    /** @var PageEntity|null */
    public $page;

    /** @var PageRepository @inject */
    public $pageRepo;

    /** @var Paginator|null */
    public $paginator;

    /** @var PaginationFactory @inject */
    public $paginationFactory;



    /**
     * @param $url string
     * @return void
     * @throws BadRequestException
     */
    public function actionDetail(string $url)
    {
        try {
            $articleFacade = $this->articleFacadeFactory->create();
            $articleDTO = $articleFacade->getOnePublishedByUrlAndLanguageId($url, $this->language->getId());
            $this->article = $articleDTO->getArticle();

            //breadcrumb
            $pages = $articleFacade->findPublishedModulePagesByArticleId($articleDTO->getArticle()->getId());
            if ($pages) {
                $page = end($pages);
                $this->setBreadcrumb($page);
            }
            $this->breadcrumb->addItem(new Item($articleDTO->getArticle()->getName()));

            $this->template->article = $articleDTO;
            $this->template->title = $articleDTO->getArticle()->getResolvedTitle();
            $this->template->metaDescription = $articleDTO->getArticle()->getDescriptionSeo();
        } catch (ArticleFacadeException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
     * @param $url string page url
     * @param $category string category url
     * @param $pagination int pagination
     * @return void
     * @throws BadRequestException
     */
    public function actionList(string $url, string $category = NULL, int $pagination = 1)
    {
        try {
            $this->page = $this->pageRepo->getOnePublishedByUrlAndLanguageIdAndType($url, $this->language->getId(), PageEntity::ARTICLES_TYPE);
            $this->module = $this->moduleRepo->getOneById($this->page->getArticleModuleId());
            $this->articleCategory = $category !== NULL ? $this->articleCategoryRepo->getOneByUrlAndLanguageId($category, $this->language->getId()) : NULL;
            $this->paginator = new Paginator();
            $this->paginator->setItemCount(0);

            //breadcrumb
            $this->setBreadcrumb($this->page);
            $this->articleCategory ? $this->breadcrumb->addItem(new Item($this->articleCategory->getName())) : NULL;

            //load data
            $this->articleCategories = $this->articleCategoryRepo->findByModuleIdAndLanguageId($this->module->getId(), $this->language->getId());
            if ($this->articleCategories) {
                $categoryId = $this->articleCategory ? [$this->articleCategory->getId()] : Entities::getProperty($this->articleCategories, 'id');
                $articleCount = $this->articleRepo->countPublishedByMoreCategoryId($categoryId);
                $this->paginator->setPage($pagination);
                $this->paginator->setItemsPerPage(12);
                $this->paginator->setItemCount($articleCount->getCount());
                $this->articles = $this->articleRepo->findPublishedByMoreCategoryIdAndOffsetAndLimit($categoryId, $this->paginator->getOffset(), $this->paginator->getLength());
            }

            $this->template->articleCategory = $this->articleCategory;
            $this->template->page = $this->page;
            $this->template->title = $this->articleCategory ? $this->articleCategory->getResolvedTitle() : $this->page->getResolvedTitle();
            $this->template->metaDescription = $this->articleCategory ? $this->articleCategory->getDescriptionSeo() : $this->page->getDescriptionSeo();
            $this->page->getTemplate() && $this->template->setFile($this->page->getTemplatePath());
            $this->template->ogTitle = $this->page->getTitleOg();
            $this->template->ogDescription = $this->page->getDescriptionOg();
        } catch (NotFoundException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
     * @return ArticleList
     */
    public function createComponentArticleList(): ArticleList
    {
        $list = $this->articleListFactory->create();
        foreach ($this->articles as $article) {
            $list->addArticle($article);
        }
        return $list;
    }



    /**
     * @return CategoryList
     */
    public function createComponentArticleCategoryList(): CategoryList
    {
        return $this->articleCategoryListFactory->create($this->page, $this->articleCategories);
    }



    /**
     * @return ArticleRelatedList
     */
    public function createComponentArticleRelatedList(): ArticleRelatedList
    {
        $list = $this->articleRelatedListFactory->create();
        $list->setArticle($this->article);
        return $list;
    }



    /**
     * @return Pagination
     */
    public function createComponentPagination(): Pagination
    {
        return $this->paginationFactory->create($this->paginator);
    }



    /**
     * @param $page PageEntity
     * @return PageEntity
     */
    private function setBreadcrumb(PageEntity $page) : PageEntity
    {
        $parentPage = $page->getParentPage();
        if ($parentPage) {
            $this->setBreadcrumb($parentPage);
        }
        $this->breadcrumb->addItem(new Item($page->getName(), $page->getFrontendLink($this->context->getByType(LinkGenerator::class))));
        return $page;
    }
}