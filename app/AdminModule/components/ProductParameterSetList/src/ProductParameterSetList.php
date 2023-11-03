<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductParameterSetList;

use App\AdminModule\Components\ProductVariantForm\ProductTrait;
use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Product\Parameter\ParameterStorageException;
use App\Product\Parameter\ParameterStorageFacadeFactory;
use App\Product\Parameter\ProductParameter;
use App\Product\Parameter\ProductParameterRepository;
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
class ProductParameterSetList extends GridoComponent
{


    use GroupTranslationTrait;
    use ProductTrait;

    /** @var Context */
    protected $database;

    /** @var LocalizationResolver */
    protected $localizationResolver;

    /** @var ParameterStorageFacadeFactory */
    private $parameterStorageFacadeFactory;

    /** @var ProductParameterGroupTranslationRepository */
    private $productParameterGroupTranslationRepo;

    /** @var ProductParameterRepository */
    private $productParameterRepo;



    public function __construct(Context $context,
                                GridoFactory $gridoFactory,
                                ParameterStorageFacadeFactory $parameterStorageFacadeFactory,
                                ProductParameterGroupTranslationRepository $productParameterGroupTranslationRepo,
                                ProductParameterRepository $productParameterRepository)
    {
        parent::__construct($gridoFactory);
        $this->database = $context;
        $this->localizationResolver = new LocalizationResolver();
        $this->parameterStorageFacadeFactory = $parameterStorageFacadeFactory;
        $this->productParameterGroupTranslationRepo = $productParameterGroupTranslationRepo;
        $this->productParameterRepo = $productParameterRepository;

    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $language = $this->localizationResolver->getDefault();
        $productParameter = ProductParameter::getAnnotation();
        $parameterTranslation = ProductParameterTranslationEntity::getAnnotation();
        $parameterGroupTranslation = ProductParameterGroupTranslationEntity::getAnnotation();

        $source = new RepositorySource($this->productParameterRepo);
        $source->setMethodCount('countJoined');
        $source->setRepositoryMethod('findJoined');
        $source->filter([
            $parameterTranslation->getPropertyByName('languageId')->getColumn()->getName() . ' = ' . $language->getId(),
            $parameterGroupTranslation->getPropertyByName('languageId')->getColumn()->getName() . ' = ' . $language->getId(),
            ['productId', '=', $this->getProduct()->getId()],
        ]);
        $source->setDefaultSort($parameterGroupTranslation->getPropertyByName('name')->getColumn()->getName(), 'ASC');

        $grid = $this->getBaseGrid();
        $grid->setModel($source);

        //actions
        $grid->setPrimaryKey($productParameter->getPropertyByName('id')->getColumn()->getName());
        $grid->addActionHref('removeParameter', '', $this->getName() . '-removeParameter!')
            ->setIcon('trash')
            ->setCustomRender(function ($row) use ($productParameter, $parameterTranslation, $parameterGroupTranslation) {
                $link = $this->link('removeParameter!', ['id' => $row[$productParameter->getPropertyByName('id')->getColumn()->getName()]]);
                $parameterName = $row[$parameterTranslation->getPropertyByName('value')->getColumn()->getName()];
                $groupName = $row[$parameterGroupTranslation->getPropertyByName('name')->getColumn()->getName()];
                $confirm = sprintf('Opravdu si přejete smazat parametr \'%s\' ze skupiny \'%s\' ?', $parameterName, $groupName);
                return sprintf('<a href="%s" 
                                   class="grid-action-removeVariant btn btn-default btn-xs btn-mini" 
                                   data-grido-confirm="%s"><i class="fa fa-trash"></i></a>', $link, $confirm);
            });

        return $grid;
    }



    /**
     * Handle for remove parameter.
     * @param $id int id of parameter
     */
    public function handleRemoveParameter(int $id)
    {
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $storageFacade = $this->parameterStorageFacadeFactory->create();
            $storageFacade->remove($id);
            $this->database->commit();
            $presenter->flashMessage('Parametr byl smazán.', 'success');
            $presenter->redirect('this');
        } catch (ParameterStorageException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @return Grid
     */
    protected function getBaseGrid() : Grid
    {
        $language = $this->localizationResolver->getDefault();
        $parameterTranslation = ProductParameterTranslationEntity::getAnnotation();
        $parameterGroupTranslation = ProductParameterGroupTranslationEntity::getAnnotation();
        $groupList = $this->getGroupList($this->productParameterGroupTranslationRepo, $language);

        $grid = $this->gridoFactory->create();

        //columns
        $group = $grid->addColumnText($parameterGroupTranslation->getPropertyByName('id')->getColumn()->getName(), 'Skupina');
        $group->getHeaderPrototype()->style['width'] = '45%';
        $group->setSortable()->setFilterSelect(Arrays::mergeTree(['' => ''], $groupList));
        $group->setCustomRender(function ($row) use ($parameterGroupTranslation) {
            return $row[$parameterGroupTranslation->getPropertyByName('name')->getColumn()->getName()];
        });
        $parameter = $grid->addColumnText($parameterTranslation->getPropertyByName('value')->getColumn()->getName(), 'Parametr');
        $parameter->getHeaderPrototype()->style['width'] = '45%';
        $parameter->setSortable()->setFilterText();

        return $grid;
    }
}