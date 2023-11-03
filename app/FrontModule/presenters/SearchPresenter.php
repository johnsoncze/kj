<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Article\ArticleEntity;
use App\Article\ArticleRepository;
use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use App\FrontModule\Components\Article\ArticleList\ArticleList;
use App\FrontModule\Components\Article\ArticleList\ArticleListFactory;
use App\FrontModule\Components\Category\Filtration\Filter\PriceRange;
use App\FrontModule\Components\Category\Filtration\Filter\SortFilter;
use App\FrontModule\Components\Pagination\Pagination;
use App\FrontModule\Components\Pagination\PaginationFactory;
use App\FrontModule\Components\Product\Filtration\Filter\StockFilter;
use App\FrontModule\Components\Product\ProductList\ProductList;
use App\FrontModule\Components\Product\ProductList\ProductListFactory;
use App\FrontModule\Components\Search\Form\SearchForm;
use App\FrontModule\Components\Search\Product\Filtration\Filtration;
use App\FrontModule\Components\Search\Product\Filtration\FiltrationFactory;
use App\Product\ProductDTO;
use App\Product\ProductFindFacade;
use App\Product\ProductFindFacadeFactory;
use App\Product\ProductPublishedRepository;
use App\Remarketing\Code\CodeDTO;
use Nette\Application\AbortException;
use Nette\Utils\Paginator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SearchPresenter extends AbstractPresenter
{


    /** @var string keys of result types */
    const ARTICLE_RESULT = 'article';
    const CATEGORY_RESULT = 'category';
    const PRODUCT_RESULT = 'default';

    /** @var ArticleListFactory @inject */
    public $articleListFactory;

    /** @var ArticleRepository @inject */
    public $articleRepo;

    /** @var ArticleEntity[]|array */
    public $articles = [];

    /** @var CategoryEntity[]|array */
    public $categories = [];

    /** @var CategoryRepository @inject */
    public $categoryRepo;

    /** @var FiltrationFactory @inject */
    public $filtrationFactory;

    /** @var string @persistent */
    public $query;

    /** @var array */
    public $productParametersFiltration = [];

    /** @var ProductFindFacade|null */
    private $productFindFacade;

    /** @var ProductFindFacadeFactory @inject */
    public $productFindFacadeFactory;

    /** @var array found out products id after filter */
    private $productId = [];

    /** @var array found out products id before apply filter */
    private $productIdForPriceRange = [];

    /** @var PaginationFactory @inject */
    public $paginationFactory;

    /** @var Paginator|null */
    public $paginator;

    /** @var array */
    public $resultTypes = [
        self::ARTICLE_RESULT => [
            'count' => 0,
            'action' => self::ARTICLE_RESULT,
        ],
        self::CATEGORY_RESULT => [
            'count' => 0,
            'action' => self::CATEGORY_RESULT,
        ],
        self::PRODUCT_RESULT => [
            'count' => 0,
            'action' => self::PRODUCT_RESULT,
        ],
    ];

    /** @var ProductListFactory @inject */
    public $productListFactory;

    /** @var ProductDTO[]|array */
    private $products = [];



    public function startup()
    {
        parent::startup();

        if(is_null($this->query))
        $this->query = '';
        //count result
        $languageId = $this->language->getId();
        $this->productFindFacade = $this->productFindFacadeFactory->create();
        $this->productId = $this->productFindFacade->findPublishedMoreIdBySearch($languageId, $this->query, $this->getProductFilter());
        $this->productIdForPriceRange = $this->productId = count($this->productId) === 1 ? $this->productId : $this->productFindFacade->findProductIdWithGroupedVariantsByProductId($this->productId);
        $this->resultTypes[self::PRODUCT_RESULT]['count'] = count($this->productId);
        $this->resultTypes[self::CATEGORY_RESULT]['count'] = $this->categoryRepo->countPublishedByLanguageIdAndSearch($languageId, $this->query)->getCount();
        $this->resultTypes[self::ARTICLE_RESULT]['count'] = $this->articleRepo->countBySearch($languageId, $this->query)->getCount();

        $this->resolveRedirect();
        $this->remarketingCode->setPageType(CodeDTO::PAGE_TYPE_SEARCH_RESULTS);

        $this->template->setFile(__DIR__ . '/templates/Search/default.latte');
        $this->template->title = $this->translator->translate('presenterFront.search.' . $this->getAction());
        $this->template->query = $this->query;
        $this->template->indexSeo = FALSE;
    }



    /**
     * @param $query string
     * @param $pagination int
     * @return void
     */
    public function actionArticle(string $query, int $pagination = 1)
    {
        $productResultCount = $this->resultTypes[self::ARTICLE_RESULT]['count'];
        if ($productResultCount) {
            $this->paginator = new Paginator();
            $this->paginator->setItemCount($productResultCount);
            $this->paginator->setPage($pagination);
            $this->paginator->setItemsPerPage(20);

            $this->articles = $this->articleRepo->findBySearch($this->language->getId(), $query, $this->paginator->getLength(), $this->paginator->getOffset());
        }

        $this->template->articles = $this->articles;
    }



    /**
     * @param $query string
     * @param $pagination int
     */
    public function actionCategory(string $query, int $pagination = 1)
    {
        $categoryResultCount = $this->resultTypes[self::CATEGORY_RESULT]['count'];
        if ($categoryResultCount) {
            $this->paginator = new Paginator();
            $this->paginator->setItemCount($categoryResultCount);
            $this->paginator->setPage($pagination);
            $this->paginator->setItemsPerPage(20);

            $this->categories = $this->categoryRepo->findPublishedByLanguageIdAndSearch($this->language->getId(), $query, $this->paginator->getLength(), $this->paginator->getOffset());
        }

        $this->template->categories = $this->categories;
    }



    /**
     * @param $query string
     * @param $pagination int
     * @param $priceFrom string|null
     * @param $priceTo string|null
     * @param $sort string|null
     * @param $stock string|null
     * @return void
     */
    public function actionDefault(string $query, int $pagination = 1, string $priceFrom = NULL, string $priceTo = NULL, string $sort = NULL, string $stock = NULL)
    {
        $productResultCount = $this->resultTypes[self::PRODUCT_RESULT]['count'];
        if ($productResultCount) {
            $this->paginator = new Paginator();
            $this->paginator->setItemCount($productResultCount);
            $this->paginator->setPage($pagination);
            $this->paginator->setItemsPerPage(20);

            $this->products = $this->productFindFacade->findPublishedByMoreIdAndLimitAndOffset($this->productId, $this->paginator->getLength(), $this->paginator->getOffset(), $this->getProductFilter());
        }

        $this->template->products = $this->products;
    }



    /**
     * @return void
     */
    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->result = $this->resultTypes;
        $this->template->summaryResultCount = $this->getSummaryResultCount();
    }



    /**
     * @return ArticleList
     */
    public function createComponentArticleList() : ArticleList
    {
        $list = $this->articleListFactory->create();
        foreach ($this->articles as $article) {
            $list->addArticle($article);
        }
        return $list;
    }



    /**
     * @return Filtration
     */
    public function createComponentProductFiltration() : Filtration
    {
        $filtration = $this->filtrationFactory->create();
        if ($this->productIdForPriceRange) {
            $prices = $this->productFindFacade->findPublishedMinAndMaxPriceByMoreProductId($this->productIdForPriceRange);
            $filtration->setPriceRange($prices['min'], $prices['max']);
        }
        return $filtration;
    }



    /**
     * @return Pagination
     */
    public function createComponentPagination() : Pagination
    {
        return $this->paginationFactory->create($this->paginator);
    }



    /**
     * @return ProductList
     */
    public function createComponentProductList() : ProductList
    {
        $list = $this->productListFactory->create();
        $list->setProducts($this->products);
        $this->loggedUser ? $list->setCustomer($this->loggedUser->getEntity()) : NULL;
        return $list;
    }



    /**
     * @return SearchForm
     */
    public function createComponentSearchFormResult() : SearchForm
    {
        $form = $this->createComponentSearchForm();
        $form->setQuery($this->query);
        $form->setResultCount($this->getSummaryResultCount());

        return $form;
    }



    /**
     * @return int
     */
    private function getSummaryResultCount() : int
    {
        $summary = 0;
        foreach ($this->resultTypes as $value) {
            $summary += $value['count'];
        }
        return (int)$summary;
    }



    /**
     * Get product set filter.
     * @return array
     */
    private function getProductFilter() : array
    {
        $request = $this->getRequest();
        $customer = $this->loggedUser ? $this->loggedUser->getEntity() : NULL;

        $filter = [];
        $filter[SortFilter::KEY] = str_replace(SortFilter::SORT_DEFAULT, '', SortFilter::getFromHttpRequest($request));
        $filter[SortFilter::KEY] = !$filter[SortFilter::KEY] ? ProductPublishedRepository::SORT_STOCK : $filter[SortFilter::KEY];
        $filter[StockFilter::KEY] = StockFilter::getFromHttpRequest($request);
        $filter[PriceRange::PRICE_FROM_KEY] = PriceRange::getFromHttpRequest($request, $customer, PriceRange::PRICE_FROM_KEY);
        $filter[PriceRange::PRICE_TO_KEY] = PriceRange::getFromHttpRequest($request, $customer, PriceRange::PRICE_TO_KEY);

        return $filter;
    }



    /**
     * @throws AbortException
     */
    private function resolveRedirect()
    {
        $resultTypes = $this->resultTypes;
        $actualAction = $this->getAction();

        if (!$resultTypes[$actualAction]['count']) {
            foreach ($resultTypes as $value) {
                if (!$value['count']) {
                    continue;
                }

                $resultAction = $value['action'];
                if ($actualAction !== $resultAction) {
                    $this->redirect($resultAction);
                }
            }
        }
    }
}