<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Product\DiamondList\DiamondList;
use App\AdminModule\Components\Product\DiamondList\DiamondListFactory;
use App\AdminModule\Components\Product\Variant\GenerateForm\GenerateForm;
use App\AdminModule\Components\Product\Variant\GenerateForm\GenerateFormFactory;
use App\AdminModule\Components\Product\WeedingRingForm\WeedingRingForm;
use App\AdminModule\Components\Product\WeedingRingForm\WeedingRingFormFactory;
use App\AdminModule\Components\ProductBatchEditForm\ProductBatchEditForm;
use App\AdminModule\Components\ProductBatchEditForm\ProductBatchEditFormFactory;
use App\AdminModule\Components\ProductFiltrationForm\ProductFiltrationForm;
use App\AdminModule\Components\ProductFiltrationForm\ProductFiltrationFormFactory;
use App\AdminModule\Components\ProductForm\ProductForm;
use App\AdminModule\Components\ProductForm\ProductFormFactory;
use App\AdminModule\Components\ProductList\ProductList;
use App\AdminModule\Components\ProductList\ProductListFactory;
use App\AdminModule\Components\ProductNotCompletedList\ProductNotCompletedList;
use App\AdminModule\Components\ProductNotCompletedList\ProductNotCompletedListFactory;
use App\AdminModule\Components\ProductParameterSetForm\ProductParameterSetForm;
use App\AdminModule\Components\ProductParameterSetForm\ProductParameterSetFormFactory;
use App\AdminModule\Components\ProductParameterSetList\ProductParameterSetList;
use App\AdminModule\Components\ProductParameterSetList\ProductParameterSetListFactory;
use App\AdminModule\Components\ProductParentVariantList\ProductParentVariantList;
use App\AdminModule\Components\ProductParentVariantList\ProductParentVariantListFactory;
use App\AdminModule\Components\ProductRelatedForm\ProductRelatedForm;
use App\AdminModule\Components\ProductRelatedForm\ProductRelatedFormFactory;
use App\AdminModule\Components\ProductRelatedList\ProductRelatedList;
use App\AdminModule\Components\ProductRelatedList\ProductRelatedListFactory;
use App\AdminModule\Components\ProductSearchEngineForm\ProductSearchEngineForm;
use App\AdminModule\Components\ProductSearchEngineForm\ProductSearchEngineFormFactory;
use App\AdminModule\Components\ProductVariantForm\ProductVariantForm;
use App\AdminModule\Components\ProductVariantForm\ProductVariantFormFactory;
use App\AdminModule\Components\ProductVariantList\ProductVariantList;
use App\AdminModule\Components\ProductVariantList\ProductVariantListFactory;
use App\Category\CategoryFindFacadeFactory;
use App\Helpers\Arrays;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoRepositoryFactory;
use App\Product\Parameter\ProductParameter;
use App\Product\Product;
use App\Product\ProductRepositoryFactory;
use App\Product\Variant\VariantRepository;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterNotFoundException;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameter\ProductParameterTranslationEntity;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use Grido\Grid;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductPresenter extends AdminModulePresenter
{


    /** @var CategoryFindFacadeFactory @inject */
    public $categoryFindFacadeFactory;

    /** @var ProductParameterGroupEntity|null */
    private $parameterGroup;

    /** @var DiamondListFactory @inject */
    public $productDiamondListFactory;

    /** @var ProductFormFactory @inject */
    public $productFormFactory;

    /** @var ProductListFactory @inject */
    public $productListFactory;

    /** @var ProductFiltrationFormFactory @inject */
    public $productFiltrationFormFactory;

    /** @var ProductBatchEditFormFactory @inject */
    public $productBatchEditFormFactory;

    /** @var ProductNotCompletedListFactory @inject */
    public $productNotCompletedListFactory;

    /** @var ProductParameterSetFormFactory @inject */
    public $productParameterSetFormFactory;

    /** @var ProductAdditionalPhotoRepositoryFactory @inject */
    public $productAdditionalPhotoRepoFactory;

    /** @var ProductParameterRepository @inject */
    public $productParameterRepo;

    /** @var ProductParameterSetListFactory @inject */
    public $productParameterSetListFactory;

    /** @var ProductParentVariantListFactory @inject */
    public $productParentVariantListFactory;

    /** @var ProductRelatedFormFactory @inject */
    public $productRelatedFormFactory;

    /** @var ProductRelatedListFactory @inject */
    public $productRelatedListFactory;

    /** @var ProductSearchEngineFormFactory @inject */
    public $productSearchEngineFormFactory;

    /** @var ProductVariantFormFactory @inject */
    public $productVariantFormFactory;

    /** @var ProductVariantListFactory @inject */
    public $productVariantListFactory;

    /** @var VariantRepository @inject */
    public $productVariantRepo;

    /** @var null|Product */
    protected $product;

    /** @var ProductParameterTranslationEntity[]|array */
    protected $filteredParameters = [];

    /** @var GenerateFormFactory @inject */
    public $variantGenerateFormFactory;

    /** @var WeedingRingFormFactory @inject */
    public $weedingRingFormFactory;



    public function beforeRender()
    {
        parent::beforeRender();
        if ($this->product !== NULL) {
            $this->addToHeadline($this->product->getTranslation()->getFullName($this->product));
        }
    }



    /**
     * @param $type string|null product type
     * @return void
    */
    public function actionAdd(string $type = NULL)
    {
        if ($type === NULL) {
            $this->template->setFile(__DIR__ . '/templates/Product/preAdd.latte');
        }

        $this->template->type = $type;
    }



    /**
     * Action "default"
     * @param $parameter array value from more detailed filtration
     * @return void
     * @throws BadRequestException
     */
    public function actionDefault(array $parameter = [])
    {
        if ($parameter) {
            $this->loadFilteredParameters($parameter);
        }
    }



    /**
     * @param $id int product id
     */
    public function actionEditComponent(int $id)
    {
        $this->product = $this->checkRequest($id, ProductRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Product:edit', ['id' => $id]), $this->translator->translate('presenter.admin.product.edit'), 1);

        $this->template->product = $this->product;
    }



    /**
     * Action "not-completed".
     * @param $parameter array value from more detailed filtration
     * @return void
     * @throws BadRequestException
     */
    public function actionNotCompleted(array $parameter = [])
    {
        if ($parameter) {
            $this->loadFilteredParameters($parameter);
        }
    }



    /**
     * @param int $id
     */
    public function actionEdit(int $id)
    {
        $this->product = $this->checkRequest($id, ProductRepositoryFactory::class);

        $this->template->setFile(__DIR__ . '/templates/Product/add.latte');
        $this->template->product = $this->product;
        $this->template->type = $this->product->getType();
    }



    /**
     * Edit variants of product
     * @param $id int product id
     * @return void
     */
    public function actionEditVariant(int $id)
    {
        $this->product = $this->checkRequest($id, ProductRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Product:edit', ['id' => $id]), $this->translator->translate('presenter.admin.product.edit'), 1);

        $this->template->isVariant = $this->isVariant($this->product);
        $this->template->product = $this->product;
    }

    /**
     * Edit selected product variants
     * @param $id int product id
     * @return void
     */
    public function actionEditBatch(int $id)
    {
        $this->product = $this->checkRequest($id, ProductRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Product:edit', ['id' => $id]), $this->translator->translate('presenter.admin.product.edit'), 1);

        $this->template->isVariant = $this->isVariant($this->product);
        $this->template->product = $this->product;
    }



    /**
     * Edit parameters.
     * @param $id int product id
     * @return void
     */
    public function actionEditParameter(int $id)
    {
        $this->product = $this->checkRequest($id, ProductRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Product:edit', ['id' => $id]), $this->translator->translate('presenter.admin.product.edit'), 1);

        $this->template->product = $this->product;
    }



    /**
     * Edit related products.
     * @param $id int product id
     * @return void
     */
    public function actionEditRelated(int $id)
    {
        $this->product = $this->checkRequest($id, ProductRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Product:edit', ['id' => $id]), $this->translator->translate('presenter.admin.product.edit'), 1);

		$this->template->isVariant = $this->isVariant($this->product);
        $this->template->product = $this->product;
    }



    /**
     * Edit product search engine.
     * @param $id int product id
     * @return void
     */
    public function actionEditProductSearchEngine(int $id)
    {
        $this->product = $this->checkRequest($id, ProductRepositoryFactory::class);
        $this->_navigation->addLink($this->link('Product:edit', ['id' => $id]), $this->translator->translate('presenter.admin.product.edit'), 1);

        $this->template->product = $this->product;
    }



    /**
	 * Generate variants for product.
	 * @param $id int
	 * @param $parameterGroupId int|null
	 * @return void
    */
	public function actionGenerateVariant(int $id, int $parameterGroupId = NULL)
	{
		$this->product = $this->checkRequest($id, ProductRepositoryFactory::class);
		$parameterGroupId && $this->parameterGroup = $this->checkRequest($parameterGroupId, ProductParameterGroupRepository::class);
		$this->_navigation->addLink($this->link('Product:editVariant', ['id' => $id]), $this->translator->translate('presenter.admin.product.editvariant'), 1);
		$this->_navigation->addLink($this->link('Product:edit', ['id' => $id]), $this->translator->translate('presenter.admin.product.edit'), 1);

		$this->template->product = $this->product;
		$this->template->parameterGroup = $this->parameterGroup;
	}



    /**
     * @param $id int product id
     * @return void
     * @throws BadRequestException
     */
    public function actionView(int $id)
    {
        $this->product = $this->checkRequest($id, ProductRepositoryFactory::class);

        $categoryFindFacade = $this->categoryFindFacadeFactory->create();
        $categories = $categoryFindFacade->findByProductId($this->product->getId());

        $this->template->product = $this->product;
        $this->template->categories = $categories;
    }



    /**
     * @return DiamondList
    */
    public function createComponentProductDiamondList() : DiamondList
    {
        $list = $this->productDiamondListFactory->create();
        $list->setProduct($this->product);
        return $list;
    }



    /**
     * @return ProductFiltrationForm
     */
    public function createComponentProductFiltrationForm() : ProductFiltrationForm
    {
        $form = $this->productFiltrationFormFactory->create();
        foreach ($this->filteredParameters as $parameter) {
            $form->addParameter($parameter);
        }
        return $form;
    }

    /**
     * @return ProductBatchEditForm
     */
    public function createComponentProductBatchEditForm() : ProductBatchEditForm
    {
        return $this->productBatchEditFormFactory->create();
    }



    /**
     * @return ProductForm
     */
    public function createComponentProductForm() : ProductForm
    {
        $type = $this->getParameter('type');
        $form = $this->productFormFactory->create();
        $type ? $form->setType($type) : NULL;

        //load and set parts of the product
        if ($this->product instanceof Product) {
            $productAdditionalPhotoRepo = $this->productAdditionalPhotoRepoFactory->create();

            $form->setProduct($this->product);
            foreach ($productAdditionalPhotoRepo->findByProductId((int)$this->product->getId()) ?: [] as $photo) {
                $form->addProductAdditionalPhoto($photo);
            }
        }
        return $form;
    }



    /**
     * @return ProductList
     */
    public function createComponentProductList() : ProductList
    {
        $list = $this->productListFactory->create();
        $this->setMoreDetailedFiltrationOnProductList($list);
        return $list;
    }



    /**
	 * @return ProductParentVariantList
    */
	public function createComponentProductParentVariantList() : ProductParentVariantList
	{
		$list = $this->productParentVariantListFactory->create();
		$list->setProduct($this->product);
		return $list;
	}



    /**
     * @return ProductNotCompletedList
     */
    public function createComponentProductNotCompletedList() : ProductNotCompletedList
    {
        $list = $this->productNotCompletedListFactory->create();
        $this->setMoreDetailedFiltrationOnProductList($list);
        return $list;
    }



    /**
     * @return ProductRelatedForm
     */
    public function createComponentProductRelatedForm() : ProductRelatedForm
    {
        $form = $this->productRelatedFormFactory->create();
        $form->setProduct($this->product);
        return $form;
    }



    /**
     * @return ProductRelatedList
     */
    public function createComponentProductRelatedList() : ProductRelatedList
    {
        $form = $this->productRelatedListFactory->create();
        $form->setProduct($this->product);
        return $form;
    }



    /**
     * @return ProductSearchEngineForm
     */
    public function createComponentProductSearchEngineForm() : ProductSearchEngineForm
    {
        $form = $this->productSearchEngineFormFactory->create();
        $form->setProduct($this->product);
        return $form;
    }



    /**
     * @return ProductParameterSetForm
     */
    public function createComponentParameterSetForm() : ProductParameterSetForm
    {
        $form = $this->productParameterSetFormFactory->create();
        $form->setProduct($this->product);
        return $form;
    }



    /**
     * @return ProductParameterSetList
     */
    public function createComponentParameterSetList() : ProductParameterSetList
    {
        $list = $this->productParameterSetListFactory->create();
        $list->setProduct($this->product);
        return $list;
    }



    /**
     * @return Form
     * @throws AbortException
    */
    public function createComponentProductTypeForm() : Form
    {
        $typeList = Arrays::toPair(Product::getTypes(), 'key', 'translation');

        $form = new Form();
        $form->addSelect('type', 'Typ*', $typeList)
            ->setPrompt('- vyberte -')
            ->setAttribute('class', 'form-control');
        $form->addSubmit('submit', 'Vybrat')
            ->setAttribute('class', 'btn btn-success');
        $form->onSuccess[] = function(Form $form) {
            $values = $form->getValues();
            $this->redirect('Product:add', ['type' => $values->type]);
        };
        return $form;
    }



    /**
     * @return ProductVariantForm
     */
    public function createComponentProductVariantForm() : ProductVariantForm
    {
        $form = $this->productVariantFormFactory->create();
        $form->setProduct($this->product);
        return $form;
    }



    /**
     * @return ProductVariantList
     */
    public function createComponentProductVariantList() : ProductVariantList
    {
        $list = $this->productVariantListFactory->create();
        $list->setProduct($this->product);
        return $list;
    }



    /**
	 * @return GenerateForm
    */
	public function createComponentVariantGenerateForm() : GenerateForm
	{
		$form = $this->variantGenerateFormFactory->create();
		$this->parameterGroup && $form->setParameterGroup($this->parameterGroup);
		$form->setProduct($this->product);
		return $form;
	}



    /**
     * @return WeedingRingForm
    */
    public function createComponentWeedingRingForm() : WeedingRingForm
    {
        $type = $this->getParameter('type');
        $form = $this->weedingRingFormFactory->create();
        $type ? $form->setType($type) : NULL;
        $this->product ? $form->setProduct($this->product) : NULL;
        return $form;
    }



    /**
     * Set callback with more detailed filtration on product list.
     * @param $list ProductList
     * @return ProductList
     */
    private function setMoreDetailedFiltrationOnProductList(ProductList $list) : ProductList
    {
        $parameters = $this->getParameter('parameter');
        if ($parameters) {
            $list->setGridCallback(function (Grid $grid, ProductList $productList) use ($parameters) {
                $productParameterRelation = ProductParameter::getAnnotation();
                $source = $grid->getModel();
                //todo použít metodu z repozitáře
                $subquery = sprintf('(SELECT %s FROM %s WHERE %s IN (\'%s\') GROUP BY %1$s HAVING COUNT(*) = \'%d\')',
                    $productParameterRelation->getPropertyByName('productId')->getColumn()->getName(),
                    $productParameterRelation->getTable()->getName(),
                    $productParameterRelation->getPropertyByName('parameterId')->getColumn()->getName(),
                    implode('\',\'', $parameters),
                    count($parameters));
                $source->filter([['id', 'IN.SQL', $subquery]]);
            });
        }
        return $list;
    }



    /**
     * @param $parameterId array
     * @return ProductParameterEntity[]|array
     * @throws BadRequestException
     */
    private function loadFilteredParameters(array $parameterId = []) : array
    {
        try {
            $this->filteredParameters = $this->productParameterRepo->getByMoreId($parameterId);
            return $this->filteredParameters;
        } catch (ProductParameterNotFoundException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
	 * @param $product Product
	 * @return bool
    */
    private function isVariant(Product $product) : bool
	{
		return (bool)$this->productVariantRepo->findOneByProductVariantId($product->getId());
	}
}