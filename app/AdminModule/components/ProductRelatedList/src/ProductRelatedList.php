<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductRelatedList;

use App\AdminModule\Components\ProductVariantForm\ProductTrait;
use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Helpers\Entities;
use App\Libs\FileManager\FileManager;
use App\Product\Photo\PhotoTrait;
use App\Product\Product;
use App\Product\ProductFindFacadeFactory;
use App\Product\ProductRepository;
use App\Product\Related\Related;
use App\Product\Related\RelatedFacadeException;
use App\Product\Related\RelatedFacadeFactory;
use App\Product\Related\RelatedRepository;
use App\Product\Translation\ProductTranslation;
use Grido\Grid;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductRelatedList extends GridoComponent
{


    use PhotoTrait;
    use ProductTrait;

    /** @var Context */
    private $database;

    /** @var FileManager */
    private $fileManager;

    /** @var LocalizationResolver */
    private $localizationResolver;

    /** @var ProductFindFacadeFactory */
    private $productFindFacadeFactory;

    /** @var ProductRepository */
    private $productRepo;

    /** @var RelatedFacadeFactory */
    private $relatedFacadeFactory;

    /** @var RelatedRepository */
    private $relatedRepo;



    public function __construct(Context $context,
                                FileManager $fileManager,
                                GridoFactory $gridoFactory,
								ProductFindFacadeFactory $productFindFacadeFactory,
                                ProductRepository $productRepository,
                                RelatedFacadeFactory $relatedFacadeFactory,
                                RelatedRepository $relatedRepository)
    {
        parent::__construct($gridoFactory);
        $this->database = $context;
        $this->fileManager = $fileManager;
        $this->productFindFacadeFactory = $productFindFacadeFactory;
        $this->productRepo = $productRepository;
        $this->localizationResolver = new LocalizationResolver();
        $this->relatedFacadeFactory = $relatedFacadeFactory;
        $this->relatedRepo = $relatedRepository;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $types = Related::getTypes(TRUE);
        $productAnnotation = Product::getAnnotation();
        $productTranslationAnnotation = ProductTranslation::getAnnotation();
        $relatedProductAnnotation = Related::getAnnotation();
        $masterProduct = $this->getMasterProduct($this->product);

        $model = new RepositorySource($this->relatedRepo);
        $model->setMethodCount('countJoined');
        $model->setRepositoryMethod('findJoined');
        $model->setDefaultSort($productAnnotation->getPropertyByName('code')->getColumn()->getName(), 'ASC');
        $model->filter([
            $productTranslationAnnotation->getPropertyByName('languageId')->getColumn()->getName() . ' = ' . $this->localizationResolver->getDefault()->getId(),
            ['productId', '=', $masterProduct->getId()],
        ]);
        $model->setModifyData([$this, 'modifyRepositoryResult']);

        $grid = $this->gridoFactory->create();
        $grid->setModel($model);

        //columns
        $code = $grid->addColumnText($productAnnotation->getPropertyByName('code')->getColumn()->getName(), 'Kód produktu');
        $code->getHeaderPrototype()->style['width'] = '20%';
        $code->setSortable()->setFilterText();

        $photo = $grid->addColumnText('photo', 'Fotografie');
        $photo->setCustomRender(function ($product) {
            return $this->getThumbnailToPhoto($product['productObject'], $this->fileManager, $this->getPresenter()->context);
        });

        $name = $grid->addColumnText($productTranslationAnnotation->getPropertyByName('name')->getColumn()->getName(), 'Produkt');
        $name->getHeaderPrototype()->style['width'] = '40%';
        $name->setSortable()->setFilterText();

        $type = $grid->addColumnText($relatedProductAnnotation->getPropertyByName('type')->getColumn()->getName(), 'Typ');
        $type->setReplacement($types);
        $type->setSortable();
        $type->setFilterSelect(Arrays::mergeTree(['' => ''], $types));

        //actions
        $grid->setPrimaryKey($relatedProductAnnotation->getPropertyByName('id')->getColumn()->getName());
        $grid->addActionHref('detail', '', 'Product:workaround') //workaround destination
        ->setIcon('eye')
            ->setCustomRender(function ($row) {
                $link = $this->getPresenter()->link('Product:edit', ['id' => $row['pr_related_product_id']]);
                return sprintf('<a href="%s" 
                                   class="btn btn-default btn-xs btn-mini"><i class="fa fa-eye"></i></a>', $link);
            });

        //remove related products is possible only from master product
        if ($masterProduct->getId() === $this->product->getId()) {
			$grid->addActionHref('delete', '', $this->getName() . '-delete!')
				->setIcon('trash')
				->setCustomRender(function ($row) use ($productTranslationAnnotation, $relatedProductAnnotation, $types) {
					$name = $row[$productTranslationAnnotation->getPropertyByName('name')->getColumn()->getName()];
					$type = $row[$relatedProductAnnotation->getPropertyByName('type')->getColumn()->getName()];
					$link = $this->link('delete!', ['id' => $row[$relatedProductAnnotation->getPropertyByName('id')->getColumn()->getName()]]);
					$confirm = sprintf('Opravdu si přejete smazat produkt \'%s\' s typem \'%s\' ?', $name, $types[$type] ?? NULL);
					return sprintf('<a href="%s" 
                                   class="grid-action-removeVariant btn btn-default btn-xs btn-mini" 
                                   data-grido-confirm="%s"><i class="fa fa-trash"></i></a>', $link, $confirm);
				});
		}

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * Handle for 'delete' signal.
     * @param $id int
     */
    public function handleDelete(int $id)
    {
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $facade = $this->relatedFacadeFactory->create();
            $facade->remove($id);
            $this->database->commit();

            $presenter->flashMessage('Produkt byl smazán.', 'success');
            $presenter->redirect('this');
        } catch (RelatedFacadeException $exception) {
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



    /**
	 * @param $product Product
	 * @return Product
    */
    private function getMasterProduct(Product $product)
	{
		$productFindFacade = $this->productFindFacadeFactory->create();
		$productMaster = $productFindFacade->findMaster($product->getId());
		return $productMaster ?: $product;
	}
}