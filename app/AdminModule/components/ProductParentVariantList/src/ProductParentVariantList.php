<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductParentVariantList;

use App\AdminModule\Components\ProductVariantForm\ProductTrait;
use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Helpers\Entities;
use App\Libs\FileManager\FileManager;
use App\Product\Photo\PhotoTrait;
use App\Product\Product;
use App\Product\ProductRepository;
use App\Product\Translation\ProductTranslation;
use App\Product\Variant\VariantRepository;
use App\ProductParameter\ProductParameterTranslationEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepository;
use App\ProductParameterGroup\Translation\GroupTranslationTrait;
use Grido\Grid;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductParentVariantList extends GridoComponent
{


	use GroupTranslationTrait;
	use PhotoTrait;
	use ProductTrait;

	/** @var FileManager */
	private $fileManager;

	/** @var LocalizationResolver */
	private $localizationResolver;

	/** @var ProductParameterGroupTranslationRepository */
	private $productParameterGroupTranslationRepository;

	/** @var ProductRepository */
	private $productRepo;

	/** @var VariantRepository */
	private $variantRepo;


	public function __construct(FileManager $fileManager,
								GridoFactory $gridoFactory,
								ProductParameterGroupTranslationRepository $productParameterGroupTranslationRepository,
								ProductRepository $productRepository,
								VariantRepository $variantRepository)
	{
		parent::__construct($gridoFactory);
		$this->fileManager = $fileManager;
		$this->localizationResolver = new LocalizationResolver();
		$this->productParameterGroupTranslationRepository = $productParameterGroupTranslationRepository;
		$this->productRepo = $productRepository;
		$this->variantRepo = $variantRepository;
	}



	/**
	 * @return Grid
	 */
	public function createComponentList() : Grid
	{
		$groupList = $this->getGroupList($this->productParameterGroupTranslationRepository, $this->localizationResolver->getDefault());
		$language = $this->localizationResolver->getDefault();
		$productAnnotation = Product::getAnnotation();
		$productTranslationAnnotation = ProductTranslation::getAnnotation();
		$groupTranslationAnnotation = ProductParameterGroupTranslationEntity::getAnnotation();
		$productParameterTranslation = ProductParameterTranslationEntity::getAnnotation();

		$source = new RepositorySource($this->variantRepo);
		$source->setMethodCount('countParentJoined');
		$source->setRepositoryMethod('findParentJoined');
		$source->filter([
			$groupTranslationAnnotation->getPropertyByName('languageId')->getColumn()->getName() . ' = ' . $language->getId(),
			$productTranslationAnnotation->getPropertyByName('languageId')->getColumn()->getName() . ' = ' . $language->getId(),
			['productVariantId', '=', $this->getProduct()->getId()],
		]);
		$source->setDefaultSort($productAnnotation->getPropertyByName('code')->getColumn()->getName(), 'ASC');
		$source->setModifyData([$this, 'modifyRepositoryResult']);

		$grid = $this->gridoFactory->create();
		$grid->setModel($source);

		//columns
		$code = $grid->addColumnText($productAnnotation->getPropertyByName('code')->getColumn()->getName(), 'Kód produktu');
		$code->getHeaderPrototype()->style['width'] = '20%';
		$code->setSortable()->setFilterText();

		$photo = $grid->addColumnText('photo', 'Fotografie');
		$photo->setCustomRender(function ($product) {
			return $this->getThumbnailToPhoto($product['productObject'], $this->fileManager, $this->getPresenter()->context);
		});

		$productName = $grid->addColumnText($productTranslationAnnotation->getPropertyByName('name')->getColumn()->getName(), 'Produkt');
		$productName->getHeaderPrototype()->style['width'] = '30%';
		$productName->setSortable()->setFilterText();

		$productParameterGroupIdColumn = $groupTranslationAnnotation->getPropertyByName('productParameterGroupId')->getColumn();
		$groupName = $grid->addColumnText($productParameterGroupIdColumn->getName(), 'Skupina parametrů');
		$groupName->setCustomRender(function ($row) use ($groupList, $productParameterGroupIdColumn) {
			return $groupList[$row[$productParameterGroupIdColumn->getName()]];
		});
		$groupName->getHeaderPrototype()->style['width'] = '15%';
		$groupName->setSortable()->setFilterSelect(Arrays::mergeTree(['' => ''], $groupList));

		$parameterNameColumn = $productParameterTranslation->getPropertyByName('value')->getColumn();
		$parameterName = $grid->addColumnText($parameterNameColumn->getName(), 'S parametrem');
		$parameterName->getHeaderPrototype()->style['width'] = '15%';

		//actions
		$grid->setPrimaryKey('pv_id');
		$grid->addActionHref('detail', '', 'Product:workaround') //workaround destination
		->setIcon('eye')
			->setCustomRender(function ($row) {
				$link = $this->getPresenter()->link('Product:edit', ['id' => $row['pt_product_id']]);
				return sprintf('<a href="%s" 
                                   class="btn btn-default btn-xs btn-mini"><i class="fa fa-eye"></i></a>', $link);
			});

		return $grid;
	}



	public function render()
	{
		$this->template->setFile(__DIR__ . '/default.latte');
		$this->template->render();
	}



	/**
	 * Modify result of repository.
	 * @param $repositorySource RepositorySource
	 * @param $data array
	 * @return array
	 */
	public function modifyRepositoryResult(RepositorySource $repositorySource, array $data) : array
	{
		if ($data) {
			$productId = Arrays::getOneValue($data, 'p_id');
			$products = Entities::setIdAsKey($this->productRepo->findByMoreId($productId));
			foreach ($data as $key => $value) {
				$data[$key]['productObject'] = $products[$value['p_id']];
			}
		}
		return $data;
	}
}