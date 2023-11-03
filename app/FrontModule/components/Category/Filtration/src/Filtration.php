<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration;

use App\Category\CategoryEntity;
use App\Category\CategoryFiltrationRepository;
use App\FrontModule\Components\Category\Filtration\Filter\ColorList;
use App\FrontModule\Components\Category\Filtration\Filter\FilterParameters;
use App\FrontModule\Components\Category\Filtration\Filter\IFilter;
use App\FrontModule\Components\Category\Filtration\Filter\Parameter;
use App\FrontModule\Components\Category\Filtration\Filter\PriceRange;
use App\FrontModule\Components\Category\Filtration\Filter\SortFilter;
use App\FrontModule\Components\Product\Filtration\Filter\StockFilter;
use App\FrontModule\Components\Product\Filtration\FiltrationFactory;
use App\FrontModule\Presenters\CategoryPresenter;
use App\Helpers\Entities;
use App\Product\ProductFindFacadeFactory;
use App\ProductParameter\Helper\HelperRepository;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use Kdyby\Translation\ITranslator;
use Nette\Application\UI\Control;
use Nette\Application\UI\InvalidLinkException;
use Nette\Database\Connection;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Filtration extends Control
{


    /** @var string */
    const FILTER_SORT = SortFilter::KEY;
    const FILTER_STOCK = StockFilter::KEY;
    const FILTER_PRICE_FROM = PriceRange::PRICE_FROM_KEY;
    const FILTER_PRICE_TO = PriceRange::PRICE_TO_KEY;


    /** @var string */
    const PRICE_RANGE_MIN = 'min';
    const PRICE_RANGER_MAX = 'max';


    /** @var CategoryEntity|null */
    private $category;

    /** @var CategoryFiltrationRepository */
    private $categoryFiltrationRepo;

    /** @var array */
    private $categoryProductId = [];

    /** @var array */
    private $filter = [];

    /** @var FiltrationFactory */
    public $filtrationFactory;

    /** @var ProductParameterGroupRepository */
    public $parameterGroupRepo;

    /** @var HelperRepository */
    protected $parameterHelperRepo;

    /** @var ProductParameterRepository */
    public $parameterRepo;

    /** @var ProductFindFacadeFactory */
    public $productFindFacadeFactory;

    /** @var array */
    private $priceRange = [
        self::PRICE_RANGE_MIN => 0,
        self::PRICE_RANGER_MAX => 0,
    ];

    /** @var \App\Product\Parameter\ProductParameterRepository */
    public $productParameterRepo;

    /** @var ITranslator */
    private $translator;

		private $database;

    public function __construct(\App\Product\Parameter\ProductParameterRepository $productParameterRepo,
                                CategoryFiltrationRepository $categoryFiltrationRepo,
                                FiltrationFactory $filtrationFactory,
                                HelperRepository $helperRepository,
                                ITranslator $translator,
                                ProductFindFacadeFactory $productFindFacadeFactory,
                                ProductParameterGroupRepository $parameterGroupRepo,
                                ProductParameterRepository $parameterRepo,
																Connection $database)
    {
        parent::__construct();
        $this->categoryFiltrationRepo = $categoryFiltrationRepo;
        $this->filtrationFactory = $filtrationFactory;
        $this->productFindFacadeFactory = $productFindFacadeFactory;
        $this->parameterHelperRepo = $helperRepository;
        $this->parameterGroupRepo = $parameterGroupRepo;
        $this->parameterRepo = $parameterRepo;
        $this->productParameterRepo = $productParameterRepo;
        $this->translator = $translator;
				$this->database = $database;
    }



    /**
     * @param $category CategoryEntity
     * @return self
     */
    public function setCategory(CategoryEntity $category) : self
    {
        $this->category = $category;
        return $this;
    }



    /**
     * @param $productId array
     * @return self
     */
    public function setCategoryProductId(array $productId) : self
    {
        $this->categoryProductId = $productId;
        return $this;
    }



    /**
     * @param $min float
     * @param $max float
     * @return self
     */
    public function setPriceRange(float $min, float $max) : self
    {
        $this->priceRange[self::PRICE_RANGE_MIN] = $min;
        $this->priceRange[self::PRICE_RANGER_MAX] = $max;
        return $this;
    }



    /**
     * @param $filter array
     * @return self
     */
    public function setFilter(array $filter) : self
    {
        $this->filter = $filter;
        return $this;
    }



    /**
     * @return \App\FrontModule\Components\Product\Filtration\Filtration
     * @throws InvalidLinkException
     */
    public function createComponentFiltration() : \App\FrontModule\Components\Product\Filtration\Filtration
    {
        $filtration = $this->filtrationFactory->create();
        $filtration->setCancelLink($this->getPresenter()->link('Category:default', ['url' => $this->category->getUrl(), 'productParametersFiltration' => NULL]));
        foreach ($this->getFilters() as $filter) {
            $filtration->addFilter($filter);
        }
        return $filtration;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @return array|IFilter[]
     */
    private function getFilters() : array
    {
        $filters = [];
        /** @var $presenter CategoryPresenter */
        $presenter = $this->getPresenter();
        $request = $presenter->getRequest();

        //set default filters
        $stockFilter = StockFilter::create($this->translator);
        $stockFilter->setIsChecked((bool)$request->getParameter(self::FILTER_STOCK) !== FALSE);
        $filters[] = $stockFilter;

        $sorting = $request->getParameter('sort') ?: SortFilter::SORT_DEFAULT;
        $sortFilter = SortFilter::create($this->translator, self::FILTER_SORT, $sorting);
        $filters[] = $sortFilter;

        $priceFilter = new PriceRange($this->translator->translate('category.filter.price.label'), 'price');
        $priceFilter->setMin($this->priceRange[self::PRICE_RANGE_MIN]);
        $priceFilter->setMax($this->priceRange[self::PRICE_RANGER_MAX]);
        $request->getParameter(self::FILTER_PRICE_FROM) ? $priceFilter->setActualMin((float)$request->getParameter(self::FILTER_PRICE_FROM)) : NULL;
        $request->getParameter(self::FILTER_PRICE_TO) ? $priceFilter->setActualMax((float)$request->getParameter(self::FILTER_PRICE_TO)) : NULL;
        $filters[] = $priceFilter;

        $categoryFilters = $this->categoryFiltrationRepo->findByCategoryId($this->category->getId());
        if ($categoryFilters) {
            $groupId = Entities::getProperty($categoryFilters, 'productParameterGroupId');
            $groups = $this->parameterGroupRepo->getByMoreId($groupId);
            $groupId = Entities::getProperty($groups, 'id');
            $groupParameters = $this->parameterRepo->findByMoreGroupId($groupId);
            $helperId = Entities::getNotNullProperty($groupParameters, 'helperId');
            $helpers = $helperId ? $this->parameterHelperRepo->getByMoreId($helperId) : [];
            $helpers = $helpers ? Entities::setIdAsKey($helpers) : [];
            $groupParameters = Entities::toSegment($groupParameters, 'productParameterGroupId');

						
						$nonEmptyFilterItems = $this->getNonEmptyFilterItems($this->category->getId());

						foreach ($categoryFilters as $categoryFilter) {
                $groupId = (int)$categoryFilter->getProductParameterGroupId();
                $_groupParameters = $groupParameters[$groupId] ?? [];
                $group = $groups[$groupId];

                //count products for parameters
                $productCount = [];

                $translation = $group->getTranslation();
                $title = $translation->getFiltrationTitle();
                $name = (string)$categoryFilter->getId();
                $filters[] = $filter = $group->getFiltrationType() === ProductParameterGroupEntity::FILTRATION_TYPE_COLOR_LIST ? new ColorList($title, $name) : new FilterParameters($title, $name);
                $filter->setTooltip($translation->getHelp());

								
                foreach ($_groupParameters as $groupParameter) {
                    if ($groupParameter->getHelperId()) {
                        $groupParameter->setHelper($helpers[$groupParameter->getHelperId()]);
                    }
                    $parameter = new Parameter($groupParameter);
                    $parameter->setIsChecked(isset($presenter->productParametersFiltration[$groupParameter->getId()]));
                    $parameter->setProductCount($productCount[$groupParameter->getId()] ?? 0);
                    //$parameter->setIsDisabled($parameter->getProductCount() === 0);
										
										//ignorujeme filtry, ktere nemaji zadne polozky pro tuto kategorii
										if (!isset($nonEmptyFilterItems[$groupParameter->getId()])) {
												continue;
										}							
									
										
										$filter->addParameter($parameter);
                }
            }
        }
        return $filters;
    }
		

		
    /**
     * @param $categoryId int
     * @return associative array
     */
    private function getNonEmptyFilterItems($categoryId) : array
    {
				$result = $this->database->query("select distinct product_parameter_relationship.ppr_parameter_id
										from product_parameter_relationship, (select product_parameter_relationship.ppr_product_id
										from product, product_parameter_relationship, category_product_parameter
										where (product_parameter_relationship.ppr_parameter_id = category_product_parameter.cpr_product_parameter_id
										and category_product_parameter.cpr_category_id = ?
										and product_parameter_relationship.ppr_product_id = product.p_id
										and product.p_state = 'publish')) as category_products
										where (product_parameter_relationship.ppr_product_id = category_products.ppr_product_id)", $categoryId);

				$filter_items = array();
				foreach ($result as $row) {
						$filter_items[$row->ppr_parameter_id] = 1;
				}

				
				return $filter_items;
    }		
		
}