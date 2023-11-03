<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\CollectionList\ProductList;

use App\Category\CategoryEntity;
use App\Category\Product\Related\Product;
use App\Category\Product\Related\ProductFacadeException;
use App\Category\Product\Related\ProductFacadeFactory;
use App\Category\Product\Related\ProductRepository;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use Grido\Grid;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Translation\Localization;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductList extends Control
{


    /** @var CategoryEntity|null */
    private $category;

    /** @var Context */
    private $database;

    /** @var GridoFactory */
    private $gridFactory;

    /** @var Localization */
    private $language;

    /** @var ProductFacadeFactory */
    private $productFacadeFactory;

    /** @var ProductRepository */
    private $productRepo;



    public function __construct(Context $context,
                                GridoFactory $gridoFactory,
                                ProductFacadeFactory $productFacadeFactory,
                                ProductRepository $productRepo)
    {
        parent::__construct();
        $this->database = $context;
        $this->gridFactory = $gridoFactory;
        $this->language = (new LocalizationResolver())->getActual();
        $this->productFacadeFactory = $productFacadeFactory;
        $this->productRepo = $productRepo;
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
     * @return Grid
     * @todo replace names by data from entity annotations
     */
    public function createComponentList() : Grid
    {
    	$stateList = Arrays::toPair(\App\Product\Product::getStates(), 'key', 'translation');
    	$typeList = Product::getTypeList();
    	$defaultFilter = [ //todo replace column by data from entity
    		['categoryId', '=', $this->category->getId()],
			['pt_language_id', '=', $this->language->getId()],
		];

        $model = new RepositorySource($this->productRepo);
        $model->setRepositoryMethod('findJoined');
        $model->setMethodCount('countJoined');
        $model->filter($defaultFilter);
        $model->setDefaultSort(['type', 'LENGTH(sort)', 'sort'], 'ASC');

        $grid = $this->gridFactory->create();
        $grid->setModel($model);

        //columns
        $code = $grid->addColumnText('p_code', 'Kód produktu');
        $code->setSortable()->setFilterText();
        $code->getHeaderPrototype()->style['width'] = '15%';

        $name = $grid->addColumnText('pt_name', 'Název');
        $name->setSortable()->setFilterText();
        $name->getHeaderPrototype()->style['width'] = '30%';

        $state = $grid->addColumnText('p_state', 'Stav');
        $state->setSortable()->setFilterSelect(Arrays::mergeTree(['' => ''], $stateList));
        $state->setReplacement($stateList);
        $state->getHeaderPrototype()->style['width'] = '20%';

        $type = $grid->addColumnText('clp_type', 'Typ');
        $type->setSortable()->setFilterSelect(Arrays::mergeTree(['' => ''], $typeList));
        $type->setReplacement($typeList);
        $type->getHeaderPrototype()->style['width'] = '25%';

        //actions
        $grid->setPrimaryKey('clp_id');
        $grid->addActionHref('detail', '', 'Product:workaround')//workaround destination
        ->setIcon('eye')
            ->setCustomRender(function ($row) {
                $link = $this->getPresenter()->link('Product:edit', ['id' => $row['p_id']]);
                return sprintf('<a href="%s" 
                                   class="btn btn-default btn-xs btn-mini"><i class="fa fa-eye"></i></a>', $link);
            });
        $grid->addActionHref('delete', '', $this->getName() . '-deleteProduct!')
            ->setIcon('trash')
            ->setCustomRender(function ($row) {
                $link = $this->link('deleteProduct!', ['id' => $this->category->getId(), 'productId' => $row['clp_id']]);
                $confirm = sprintf('Opravdu si přejete smazat produkt \'%s\' ?', $row['pt_name']);
                return sprintf('<a href="%s" 
                                   data-grido-confirm="%s"
                                   class="grid-action-deleteProduct btn btn-default btn-xs btn-mini"><i class="fa fa-trash"></i></a>', $link, $confirm);
            });

        return $grid;
    }



    /**
     * @param $productId int
     * @return void
     * @throws AbortException
     */
    public function handleDeleteProduct(int $productId)
    {
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $productFacade = $this->productFacadeFactory->create();
            $productFacade->remove($productId);
            $this->database->commit();

            $presenter->flashMessage('Produkt byl odstraněn.', 'success');
            $presenter->redirect('this');
        } catch (ProductFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}