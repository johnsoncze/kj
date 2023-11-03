<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductVariantList;

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
use App\Product\Variant\Tree\TreeFactory;
use App\Product\Variant\VariantRepository;
use App\Product\Variant\VariantStorageFacadeException;
use App\Product\Variant\VariantStorageFacadeFactory;
use App\ProductParameter\ProductParameterTranslationEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepository;
use App\ProductParameterGroup\Translation\GroupTranslationTrait;
use Grido\Grid;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductVariantList extends GridoComponent
{


    use GroupTranslationTrait;
    use PhotoTrait;
    use ProductTrait;

    /** @var Context */
    private $database;

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

    /** @var VariantStorageFacadeFactory */
    private $variantStorageFacadeFactory;

    /** @var TreeFactory */
    private $variantTreeFactory;



    public function __construct(Context $context,
                                FileManager $fileManager,
                                GridoFactory $gridoFactory,
                                ProductParameterGroupTranslationRepository $productParameterGroupTranslationRepository,
                                ProductRepository $productRepository,
                                TreeFactory $treeFactory,
                                VariantRepository $variantRepository,
                                VariantStorageFacadeFactory $variantStorageFacadeFactory)
    {
        parent::__construct($gridoFactory);
        $this->database = $context;
        $this->fileManager = $fileManager;
        $this->localizationResolver = new LocalizationResolver();
        $this->productParameterGroupTranslationRepository = $productParameterGroupTranslationRepository;
        $this->productRepo = $productRepository;
        $this->variantRepo = $variantRepository;
        $this->variantStorageFacadeFactory = $variantStorageFacadeFactory;
        $this->variantTreeFactory = $treeFactory;
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
        $source->setMethodCount('countJoined');
        $source->setRepositoryMethod('findJoined');
        $source->filter([
            $groupTranslationAnnotation->getPropertyByName('languageId')->getColumn()->getName() . ' = ' . $language->getId(),
            $productTranslationAnnotation->getPropertyByName('languageId')->getColumn()->getName() . ' = ' . $language->getId(),
            ['productId', '=', $this->getProduct()->getId()],
        ]);
        $source->setDefaultSort(['LENGTH(' . $productAnnotation->getPropertyByName('code')->getColumn()->getName() . ')', $productAnnotation->getPropertyByName('code')->getColumn()->getName()], 'ASC');
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
        $parameterName = $grid->addColumnText($parameterNameColumn->getName(), 'Parametr');
        $parameterName->getHeaderPrototype()->style['width'] = '15%';

        //actions
        $grid->setPrimaryKey('pv_id');
        $grid->addActionHref('detail', '', 'Product:workaround') //workaround destination
            ->setIcon('eye')
            ->setCustomRender(function ($row) {
            	if ($row['pt_product_id'] != $this->product->getId()) {
					$link = $this->getPresenter()->link('Product:edit', ['id' => $row['pt_product_id']]);
					return sprintf('<a href="%s" 
                                   class="btn btn-default btn-xs btn-mini"><i class="fa fa-eye"></i></a>', $link);
				}
				return NULL;
            });
        $grid->addActionHref("removeVariant", "", $this->getName() . "-removeVariant!")
            ->setIcon('trash')
            ->setCustomRender(function ($row) {
                $link = $this->link('removeVariant!', ['id' => $this->getProduct()->getId(), 'variantId' => $row['pv_id']]);
                $confirm = sprintf('Opravdu si přejete smazat produkt \'%s\' se skupinou \'%s\' ?', $row['pt_name'], $row['ppgt_name']);
                return sprintf('<a href="%s" 
                                   class="grid-action-removeVariant btn btn-default btn-xs btn-mini" 
                                   data-grido-confirm="%s"><i class="fa fa-trash"></i></a>', $link, $confirm);
            });

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    public function renderTree()
    {
        $this->template->product = $this->product;
        $this->template->tree = $this->variantTreeFactory->createByProductId($this->product->getId(), FALSE);
        $this->template->setFile(__DIR__ . '/tree.latte');
        $this->template->render();
    }



    /**
     * Handle for remove a variant.
     * @param $variantId int
     * @return void
     */
    public function handleRemoveVariant(int $variantId)
    {
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $variantFacade = $this->variantStorageFacadeFactory->create();
            $variantFacade->remove($variantId);
            $this->database->commit();

            $presenter->flashMessage('Varianta byla smazána.', 'success');
            $presenter->redirect('this');
        } catch (VariantStorageFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
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