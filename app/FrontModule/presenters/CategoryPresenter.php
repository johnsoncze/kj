<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Category\CategoryEntity;
use App\Category\CategoryNotFoundException;
use App\Category\CategoryRepository;
use App\Category\Product\Related\Product;
use App\Category\Product\Related\ProductRepository;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\CategoryProductParameter\CategoryProductParameterRepository;
use App\Customer\Customer;
use App\FrontModule\Components\Breadcrumb\Item;
use App\FrontModule\Components\Category\AssociatedCategory\CategoryList as AssociatedCategoryList;
use App\FrontModule\Components\Category\AssociatedCategory\CategoryListFactory as AssociatedCategoryListFactory;
use App\FrontModule\Components\Category\CategoryList\CategoryList;
use App\FrontModule\Components\Category\CategoryList\CategoryListFactory;
use App\FrontModule\Components\Category\Filtration\Filter\PriceRange;
use App\FrontModule\Components\Category\Filtration\Filter\SortFilter;
use App\FrontModule\Components\Category\Filtration\Filtration;
use App\FrontModule\Components\Category\Filtration\FiltrationFactory;
use App\FrontModule\Components\Category\SubCategoryList\SubCategoryList;
use App\FrontModule\Components\Category\SubCategoryList\SubCategoryListFactory;
use App\FrontModule\Components\Category\TopCategory\TopCategory;
use App\FrontModule\Components\Category\TopCategory\TopCategoryFactory;
use App\FrontModule\Components\Category\Articles\Articles;
use App\FrontModule\Components\Category\Articles\ArticlesFactory;
use App\FrontModule\Components\Company\Preview\PreviewFactory;
use App\FrontModule\Components\Pagination\Pagination;
use App\FrontModule\Components\Pagination\PaginationFactory;
use App\FrontModule\Components\Product\Filtration\Filter\StockFilter;
use App\FrontModule\Components\Product\ProductList\ProductList;
use App\FrontModule\Components\Product\ProductList\ProductListFactory;
use App\Helpers\Entities;
use App\Helpers\Prices;
use App\Libs\FileManager\FileManager;
use App\Product\ProductDTO;
use App\Product\ProductDTOFactory;
use App\Product\ProductFindFacadeFactory;
use App\Product\ProductPublishedRepository;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameter\ProductParameterTranslationRepository;
use App\Remarketing\Code\CodeDTO;
use App\PromoArticle\PromoArticleRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Utils\Paginator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CategoryPresenter extends AbstractPresenter
{


    /** @var AssociatedCategoryListFactory @inject */
    public $associatedCategoryListFactory;

    /** @var CategoryEntity|null */
    public $category;

    /** @var CategoryListFactory @inject */
    public $categoryListFactory;

    /** @var CategoryFiltrationGroupRepository @inject */
    public $categoryParameterCombinationRepo;

    /** @var CategoryProductParameterRepository @inject */
    public $categoryParameterRepo;

    /** @var array */
    public $categoryProductId = [];

    /** @var array */
    public $categoryProductIdBeforeFilter = [];

    /** @var CategoryRepository @inject */
    public $categoryRepo;

    /** @var PreviewFactory @inject */
    public $companyPreviewFactory;

    /** @var FiltrationFactory @inject */
    public $filtrationFactory;

    /** @var ProductFindFacadeFactory @inject */
    public $productFindFacadeFactory;

    /** @var array product parameters from filtration @persistent */
    public $productParametersFiltration = [];

    /** @var PaginationFactory @inject */
    public $paginationFactory;

    /** @var Paginator|null */
    public $paginator;

    /** @var array */
    public $priceRange = [];

    /** @var ProductDTOFactory @inject */
    public $productFactory;

    /** @var ProductListFactory @inject */
    public $productListFactory;

    /** @var ProductParameterRepository @inject */
    public $productParameterRepo;

    /** @var ProductParameterTranslationRepository @inject */
    public $productParameterTranslationRepository;

    /** @var ProductPublishedRepository @inject */
    public $productRepo;

    /** @var ProductRepository @inject */
    public $relatedProductRepo;
		
    /** @var ProductDTO[]|array */
    public $products = [];

    /** @var SubCategoryListFactory @inject */
    public $subCategoryListFactory;

    /** @var TopCategoryFactory @inject */
    public $topCategoryFactory;

    /** @var ArticlesFactory @inject */
    public $articlesFactory;
	
    /** @var PromoArticleRepository @inject */
    public $promoArticleRepository;		
		
    /** @var FileManager @inject */
    public $fileManeger;

		

    /**
     * @param $url string
     * @param $pagination int|null pagination
     * @param $_image string
     * @param $priceFrom string|null
     * @param $priceTo string|null
     * @param $sort string|null
     * @param $stock string|null
     * @return void
     * @throws BadRequestException
     */
    public function actionDefault(
        string $url,
        int $pagination = 1,
        string $_image = 'false',
        string $priceFrom = null,
        string $priceTo = null,
        string $sort = null,
        string $stock = null
    ) {
        try {
            $this->category = $this->categoryRepo->getOnePublishedByUrlAndLanguageId($url, $this->language->getId());
            $this->remarketingCode->setData([CodeDTO::DATA_CATEGORY => $this->category->getTextNavigation()]);

            $this->template->category = $this->category;
            $this->template->page = $pagination;

						$productCount = 0;

						//show image template instead of product list (collections)
            if ($_image === 'true') {
                $this->template->setFile(__DIR__ . "/templates/Category/static/uvod_kolekce.latte");								

								$products = array();
								foreach ($this->relatedProductRepo->findHomepageProductsByCategoryId($this->category->getId()) as $relatedProduct) {
										$products[] = $this->productRepo->getOneById($relatedProduct->getProductId());

								}
								$this->products = $this->productFactory->createFromProducts($products);
								$productCount = count($this->products);
						}
						else {
								$categoryParameters = $this->categoryParameterRepo->findByCategoryId($this->category->getId());

								$this->paginator = $this->createPagination($pagination, $productCount);

								$this->loadProductParametersFiltration();
								$filter = $this->getSetFilter();

								//todo move search product logic to ProductFindFacade class
								if ($categoryParameters) {
										$parameterId = Entities::getProperty($categoryParameters, 'productParameterId');
										$categoryProductId = $this->categoryProductIdBeforeFilter = $this->productRepo->findProductIdByMoreParameterIdAsCategoryParameter($parameterId);
										if ($categoryProductId) {
												$productFindFacade = $this->productFindFacadeFactory->create();
												$this->priceRange = $this->productRepo->findMinAndMaxPriceByMoreProductId($categoryProductId);
												$this->categoryProductId = $filter ? $this->productRepo->findProductIdByProductIdAndFilters($categoryProductId, $filter) : $categoryProductId;
												$groupedCategoryProductId = $this->categoryProductId ? $productFindFacade->findProductIdWithGroupedVariantsByMoreProductIdAndMoreParameterId($this->categoryProductId, $filter['parameterWithGroup'] ?? []) : [];
												$productCount = count($groupedCategoryProductId);
												$this->paginator->setItemCount($productCount);
												$this->products = $productFindFacade->findPublishedByMoreIdAndLimitAndOffset($groupedCategoryProductId, $this->paginator->getLength(), $this->paginator->getOffset(), $filter, $this->category->getId());
										}
								}
						}
						
						//template variables
						if (isset($filter['parameterId'])) {
								$categoryParameterFamily = $this->categoryParameterCombinationRepo->findOneByCategoryIdAndMoreParameterId($this->category->getId(), array_keys($filter['parameterId']));
								$categoryParameterFamily ? $this->template->follow = $categoryParameterFamily->isFollowedInSeo() : NULL;
						}

						$this->template->index = false;
						if (!isset($filter['parameterId']) || count($filter['parameterId']) == 1) {
								$this->template->index = true;
						}

						//template
						$this->category->getTemplate() ? $this->template->setFile($this->category->getTemplate(TRUE)) : NULL;
						$this->template->categoryParameterGroup = $categoryParameterFamily ?? NULL;
						$this->template->productCount = $productCount;
            

            $this->remarketingCode->setPageType(CodeDTO::PAGE_TYPE_CATEGORY);

            //breadcrumb
            $this->setBreadcrumb($this->category);

	        if (isset($categoryParameterFamily) && $categoryParameterFamily->getTitleSeo()){
		        $this->template->categoryTitle = $categoryParameterFamily->getTitleSeo();
	        }else{
		        $this->template->categoryTitle = $this->category->getTitle();
	        }
            //seo
            if (!isset($categoryParameterFamily) && isset($filter['parameterId']) && count($filter['parameterId']) == 1) {
                $parameterId = key($filter['parameterId']);
                $categorySeo = $this->database->table('category_seo')->where('category = ?', $this->category->id)->where('parameter', $parameterId)->fetch();
            }
            if (isset($categorySeo) && $categorySeo) {
                $this->template->title = $this->category->getResolvedTitle() . ' ' . $categorySeo->seo_text;
                $this->template->categoryTitle = $this->template->title;
            } else {
                $this->template->title = isset($categoryParameterFamily) && $categoryParameterFamily->getTitleSeo() ? $categoryParameterFamily->getTitleSeo() : $this->category->getResolvedTitle();
                //$this->template->categoryTitle = $this->template->title;
            }


            $this->template->metaDescription = isset($categoryParameterFamily) && $categoryParameterFamily->getDescriptionSeo() ? $categoryParameterFamily->getDescriptionSeo() : $this->category->getDescriptionSeo();

            $this->template->ogTitle = $this->category->getTitleOg();
            $this->template->ogDescription = $this->category->getDescriptionOg();

            if ($this->category->getMenuImage()) {
                $this->template->ogImage = $this->fileManeger->getThumbnail($this->category->getMenuImage(), 280, 261);
            }

						
						//promo clanky, pokud neni zvoleno, tak defaultni
						$promoArticles = array();
						if ($this->category->getPromoArticleId1() || $this->category->getPromoArticleId2() || $this->category->getPromoArticleId3()) {
								if ($this->category->getPromoArticleId1()) {
										$promoArticles[] = $this->promoArticleRepository->getOneById($this->category->getPromoArticleId1());
								}
								if ($this->category->getPromoArticleId2()) {
										$promoArticles[] = $this->promoArticleRepository->getOneById($this->category->getPromoArticleId2());
								}
								if ($this->category->getPromoArticleId3()) {
										$promoArticles[] = $this->promoArticleRepository->getOneById($this->category->getPromoArticleId3());
								}
						}
						else {
								$promoArticles = $this->promoArticleRepository->getDefault();
						}
						$this->template->promoArticles = $promoArticles;
						
						
        } catch (CategoryNotFoundException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }


    /**
     * @return AssociatedCategoryList
     */
    public function createComponentAssociatedCategoryList() : AssociatedCategoryList
    {
        $list = $this->associatedCategoryListFactory->create();
        $list->setCategory($this->category);
        return $list;
    }



    /**
     * @return CategoryList
     */
    public function createComponentCategoryList() : CategoryList
    {
        $list = $this->categoryListFactory->create();
        $list->setParentCategory($this->category);
        return $list;
    }



    /**
     * @return \App\FrontModule\Components\Company\Preview\Preview
     */
    public function createComponentCompanyPreview() : \App\FrontModule\Components\Company\Preview\Preview
    {
        return $this->companyPreviewFactory->create();
    }



    /**
     * @return Filtration
     */
    public function createComponentFiltration() : Filtration
    {
        $filtration = $this->filtrationFactory->create();
        $filtration->setCategory($this->category);
        $this->categoryProductIdBeforeFilter ? $filtration->setCategoryProductId($this->categoryProductIdBeforeFilter) : NULL;
        $filtration->setFilter($this->getSetFilter());
        if ($this->priceRange) {
            //if user is logged, price range is reduced by customer's discount
            $discount = $this->loggedUser ? Customer::DISCOUNT : 0;
            $filtration->setPriceRange(Prices::subtractPercent($this->priceRange['min'], $discount), Prices::subtractPercent($this->priceRange['max'], $discount));
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
        $customer = $this->loggedUser ? $this->loggedUser->getEntity() : NULL;
        $list = $this->productListFactory->create();
        $this->products ? $list->setProducts($this->products) : NULL;
        $customer ? $list->setCustomer($customer) : NULL;
        return $list;
    }



    /**
     * @return SubCategoryList
     */
    public function createComponentSubCategoryList() : SubCategoryList
    {
        $list = $this->subCategoryListFactory->create();
        $list->setCategory($this->category);
        return $list;
    }



    /**
     * @return TopCategory
     */
    public function createComponentTopCollection() : TopCategory
    {
        $component = $this->topCategoryFactory->create();
        $component->setParentCategory($this->category);
        return $component;
    }

		
    /**
     * @return Articles
     */
    public function createComponentArticles() : Articles
    {
        $component = $this->articlesFactory->create();
        return $component;
    }

		


    /**
     * @param $limit int
     * @param $offset int
     * @return ProductDTO[]|array
     * @throws \InvalidArgumentException
     */
    public function getRepresentativeProducts(int $limit, int $offset = 0) : array
    {
        static $loaded = FALSE;
        static $products = [];

        if ($loaded === FALSE) {

            if ($this->category === NULL) {
                throw new \InvalidArgumentException('Missing category entity.');
            }

            $categoryId = $this->category->getId();
            $productFindFacade = $this->productFindFacadeFactory->create();
            $products = $productFindFacade->findRepresentativePublishedByMoreCategoryIdAndType([$categoryId], Product::TYPE_IMAGE_TEMPLATE);
            $products = $products ? $products[$categoryId] : [];
            $loaded = TRUE;
        }

        return $products ? array_slice($products, $offset, $limit) : [];
    }



    /**
     * @return array
     */
    private function getSetFilter() : array
    {
        $filter = [];
        $request = $this->getRequest();
        $customer = $this->loggedUser ? $this->loggedUser->getEntity() : NULL;

        $filter[SortFilter::KEY] = SortFilter::getFromHttpRequest($request);
        $filter[StockFilter::KEY] = StockFilter::getFromHttpRequest($request);
        $filter[PriceRange::PRICE_TO_KEY] = PriceRange::getFromHttpRequest($request, $customer, PriceRange::PRICE_TO_KEY);
        $filter[PriceRange::PRICE_FROM_KEY] = PriceRange::getFromHttpRequest($request, $customer, PriceRange::PRICE_FROM_KEY);
        if(!$filter[SortFilter::KEY] && !$this->isRingCategory()) {
            // all except rings: sort by in_stock DESC
            $filter[SortFilter::KEY] = SortFilter::SORT_IN_STOCK;
        }

        if ($this->productParametersFiltration) {
            $filter['parameterId'] = $this->productParametersFiltration;
            $filter['groupedProductParameters'] = $this->getGroupedProductParametersFiltration($this->productParametersFiltration);
            foreach ($filter['groupedProductParameters'] as $group => $parameter) {
                foreach ($parameter as $param) {
                    $filter['parameterWithGroup'][$param] = $group;
                }
            }
        }
        return $filter;
    }

    /**
     * @return bool
     */
    private function isRingCategory(): bool
    {
        if(!$this->category) {
            return false;
        }
        if(in_array($this->category->getId(), [6, 13, 14])) {
            return true;
        }
        return false;
    }


    /**
     * @return array
     * @throws BadRequestException
     * @throws AbortException
     */
    private function loadProductParametersFiltration() : array
    {
        if ($this->productParametersFiltration) {
            $parameters = $this->productParameterTranslationRepository->findByMoreUrlAndLanguageIdAndCategoryId($this->productParametersFiltration, $this->languageEntity->getId(), $this->category->getId());
            if (!$parameters || count($this->productParametersFiltration) !== count($parameters)) {
                throw new BadRequestException(NULL, 404);
            }

            $parameters = Entities::toPair($parameters, 'url', 'productParameterId');
            $duplicatedProductParametersFiltration = $this->productParametersFiltration;
            $this->productParametersFiltration = [];

            //replace natural key by product parameter id
            foreach ($duplicatedProductParametersFiltration as $value) {
                $parameterId = $parameters[$value];
                $this->productParametersFiltration[$parameterId] = $value;
            }

            //check sorting of parameters in url
            if ($this->productParametersFiltration != array_flip($parameters)) {
                $this->redirectPermanent('this');
            }
        }
        return $this->productParametersFiltration;
    }



    /**
     * @param $parameters array
     * @return array
     */
    private function getGroupedProductParametersFiltration(array $parameters) : array
    {
        $groupedParameters = [];
        $parameters = $this->productParameterRepo->getByMoreId(array_keys($parameters));
        foreach ($parameters as $parameter) {
            $groupedParameters[$parameter->getProductParameterGroupId()][] = $parameter->getId();
        }
        return $groupedParameters;
    }



    /**
     * @param $category CategoryEntity
     * @return CategoryEntity
     */
    private function setBreadcrumb(CategoryEntity $category) : CategoryEntity
    {
        $parentCategory = $category->getParentEntity();
        $parentCategory ? $this->setBreadcrumb($parentCategory) : NULL;
        $this->breadcrumb->addItem(new Item($category->getName(), $category->getRelativeLink($this)));
        return $category;
    }



    /**
     * @param $actualPage int
     * @param $itemCount int
     * @return Paginator
    */
    private function createPagination(int $actualPage, int $itemCount = 0) : Paginator
    {
        $paginator = new Paginator();
        $paginator->setItemCount($itemCount);
        $paginator->setPage($actualPage);
        $paginator->setItemsPerPage(20);

        return $paginator;
    }
}