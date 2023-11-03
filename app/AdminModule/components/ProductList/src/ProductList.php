<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Libs\FileManager\FileManager;
use App\Product\Photo\PhotoTrait;
use App\Product\Product;
use App\Product\ProductRepositoryFactory;
use App\Product\ProductSaveFacadeException;
use App\Product\ProductSaveFacadeFactory;
use App\Product\Translation\ProductTranslation;
use Grido\Grid;
use Nette\Application\AbortException;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductList extends GridoComponent
{


    use PhotoTrait;

    /** @var Context */
    protected $database;

    /** @var ProductSaveFacadeFactory */
    protected $productFacadeFactory;

    /** @var ProductRepositoryFactory */
    protected $productRepoFactory;

    /** @var FileManager */
    protected $fileManager;

    /** @var int */
    protected $thumbnailWidth = 50;

    /** @var int */
    protected $thumbnailHeight = 50;

    /** @var callable|null */
    protected $gridCallback;



    public function __construct(Context $database,
								GridoFactory $gridoFactory,
								ProductSaveFacadeFactory $productSaveFacadeFactory,
                                ProductRepositoryFactory $productRepoFactory,
                                FileManager $fileManager)
    {
        parent::__construct($gridoFactory);
        $this->database = $database;
        $this->productFacadeFactory = $productSaveFacadeFactory;
        $this->productRepoFactory = $productRepoFactory;
        $this->fileManager = $fileManager;
    }



    /**
     * Set grid callback.
     * @param $callback callable
     * @return self
     */
    public function setGridCallback(callable $callback) : self
    {
        $this->gridCallback = $callback;
        return $this;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $translationAnnotation = ProductTranslation::getAnnotation();
        $table = $translationAnnotation->getTable();
        $nameProperty = $translationAnnotation->getPropertyByName('name');

        $states = Product::getStates();
        $stateList = Arrays::toPair($states, 'key', 'translation');

        $source = new RepositorySource($this->productRepoFactory->create());
        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $grid->addColumnText('code', 'Kód')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('photo', 'Fotografie')
            ->setCustomRender(function (Product $product) {
                return $this->getThumbnailToPhoto($product, $this->fileManager, $this->getPresenter()->context);
            });
        $grid->addColumnText('name', 'Název')
            ->setColumn(":{$table->getName()}.{$nameProperty->getColumn()->getName()}")
            ->setCustomRender(function (Product $product) {
                $translation = $product->getTranslation();
                return $translation->getName();
            })
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('externalSystemId', 'Id v externím systému')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('state', 'Stav')
            ->setReplacement($stateList)
            ->setSortable()
            ->setFilterSelect(Arrays::mergeTree(['' => ''], $stateList));

        //actions
        $grid->addActionHref('view', '', 'Product:view')
            ->setIcon('eye');
        $grid->addActionHref('edit', '', 'Product:edit')
            ->setIcon('pencil');
        $grid->addActionHref('remove', '', $this->getName() . '-deleteProduct!')
			->setCustomRender(function(Product $product) {
				$link = $this->link('deleteProduct!', ['id' => $product->getId()]);
				$confirm = sprintf('Opravdu si přejete smazat produkt \'%s\' ?', $product->getTranslation()->getName());
				return sprintf('<a href="%s" 
                                   data-grido-confirm="%s"
                                   class="grid-action-deleteProduct btn btn-default btn-xs btn-mini"><i class="fa fa-trash"></i></a>', $link, $confirm);
			});

        //styles
        $grid->getColumn('code')->getHeaderPrototype()->style['width'] = '16%';
        $grid->getColumn('name')->getHeaderPrototype()->style['width'] = '30%';
        $grid->getColumn('photo')->getHeaderPrototype()->style['width'] = '10%';
        $grid->getColumn('state')->getHeaderPrototype()->style['width'] = '15%';
        $grid->getColumn('externalSystemId')->getHeaderPrototype()->style['width'] = '10%';
        $grid->getTablePrototype()->class .= ' products';

        if (is_callable($this->gridCallback)) {
            call_user_func_array($this->gridCallback, [$grid, $this]);
        }

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



	/**
	 * @param int $id
	 * @return void
	 * @throws AbortException
	 */
    public function handleDeleteProduct(int $id)
	{
		$presenter = $this->getPresenter();

		try {
			$this->database->beginTransaction();
			$deleteFacade = $this->productFacadeFactory->create();
			$deleteFacade->delete($id);
			$this->database->commit();

			$presenter->flashMessage('Produkt byl smazán.', 'success');
		} catch (ProductSaveFacadeException $exception) {
			$this->database->rollBack();
			$presenter->flashMessage($exception->getMessage(), 'danger');
		}

		$presenter->redirect('this');
	}
}