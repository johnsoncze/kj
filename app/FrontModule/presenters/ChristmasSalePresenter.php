<?php

namespace App\FrontModule\Presenters;

use App\Article\ArticleRepository;
use App\Article\Module\Module;
use App\ArticleCategory\ArticleCategoryRepository;
use App\FrontModule\Components\Article\ArticleList\ArticleList;
use App\FrontModule\Components\Article\ArticleList\ArticleListFactory;
use App\FrontModule\Components\Category\CollectionList\CollectionList;
use App\FrontModule\Components\Category\CollectionList\CollectionListFactory;
use App\FrontModule\Components\Category\HomepageList\HomepageList;
use App\FrontModule\Components\Category\HomepageList\HomepageListFactory;
use App\Helpers\Entities;
use App\Remarketing\Code\CodeDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ChristmasSalePresenter extends AbstractPresenter
{


    /** @var ArticleCategoryRepository @inject */
    public $articleCategoryRepo;

    /** @var ArticleListFactory @inject */
    public $articleListFactory;

    /** @var ArticleRepository @inject */
    public $articleRepo;

    /** @var HomepageListFactory @inject */
    public $categoryListFactory;

    /** @var CollectionListFactory @inject */
    public $collectionSliderFactory;



    /**
     * @return void
     */
    public function actionDefault()
    {
        $this->template->recaptchaSiteKey = $this->context->getParameters()['recaptcha']['siteKey'] ?? NULL;
        $this->remarketingCode->setPageType(CodeDTO::PAGE_TYPE_HOME);
    }



    /**
     * @return HomepageList
     */
    public function createComponentCategoryList() : HomepageList
    {
        $list = $this->categoryListFactory->create();
        $list->setLanguage($this->languageEntity);
        return $list;
    }



    /**
     * @return CollectionList
     */
    public function createComponentCollectionSlider() : CollectionList
    {
        $slider = $this->collectionSliderFactory->create();
        $slider->setLanguage($this->languageEntity);
        return $slider;
    }



    /**
     * @return ArticleList
     */
    public function createComponentNewsLastArticle() : ArticleList
    {
        $list = $this->articleListFactory->create();
        $categories = $this->articleCategoryRepo->findByModuleIdAndLanguageId(Module::NEWS, $this->language->getId());
        if ($categories) {
            $categories = Entities::getProperty($categories, 'id');
            $article = $this->articleRepo->findOneLastPublishedByMoreCategoryId($categories);
            $article ? $list->addArticle($article) : NULL;
        }
        return $list;
    }



    /**
     * @return ArticleList
     */
    public function createComponentNewArticles() : ArticleList
    {
        $list = $this->articleListFactory->create();
        $categories = $this->articleCategoryRepo->findByModuleIdAndLanguageId(Module::BLOG, $this->language->getId());
        if ($categories) {
            $categories = Entities::getProperty($categories, 'id');
            $articles = $this->articleRepo->findLastPublishedByMoreCategoryId($categories);
            foreach ($articles as $article) {
                $list->addArticle($article);
            }
        }
        return $list;
    }
}
