<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Catalog\CatalogList;

use App\Catalog\Catalog;
use App\Catalog\CatalogFacadeException;
use App\Catalog\CatalogFacadeFactory;
use App\Catalog\CatalogRepository;
use App\Catalog\Translation\CatalogTranslation;
use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use Grido\Grid;
use Nette\Application\AbortException;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CatalogList extends GridoComponent
{


    /** @var CatalogFacadeFactory */
    private $catalogFacadeFactory;

    /** @var CatalogRepository */
    private $catalogRepo;

    /** @var Context */
    private $database;

    /** @var string|null */
    private $type;



    public function __construct(CatalogFacadeFactory $catalogFacadeFactory,
                                CatalogRepository $catalogRepo,
                                Context $context,
                                GridoFactory $gridFactory)
    {
        parent::__construct($gridFactory);
        $this->catalogFacadeFactory = $catalogFacadeFactory;
        $this->catalogRepo = $catalogRepo;
        $this->database = $context;
    }



    /**
     * @param $type string
     * @return self
     */
    public function setType(string $type) : self
    {
        $this->type = $type;
        return $this;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $catalogTranslationAnnotation = CatalogTranslation::getAnnotation();
        $stateList = Arrays::toPair(Catalog::getStates(), 'key', 'translation');

        $model = new RepositorySource($this->catalogRepo);
        $model->filter([['type', '=', $this->type]]);
        $model->setDefaultSort(['LENGTH(sort)', 'sort'], 'ASC');

        $grid = $this->gridoFactory->create();
        $grid->setModel($model);

        //columns
        $name = $grid->addColumnText('title', 'Název');
        $name->setColumn(sprintf(':%s.%s', $catalogTranslationAnnotation->getTable()->getName(), $catalogTranslationAnnotation->getPropertyByName('title')->getColumn()->getName()));
        $name->setSortable()->setFilterText();
        $name->getHeaderPrototype()->style['width'] = '50%';
        $name->setCustomRender(function (Catalog $catalog) {
            $translation = $catalog->getTranslation();
            return $translation->getTitle();
        });

        $addDate = $grid->addColumnDate('addDate', 'Datum přidání');
        $addDate->setDateFormat('d.m.Y H:i:s');
        $addDate->getHeaderPrototype()->style['width'] = '15%';
        $addDate->setSortable()->setFilterDateRange();

        $state = $grid->addColumnText('state', 'Stav');
        $state->setSortable()->setFilterSelect(Arrays::mergeTree(['' => ''], $stateList));
        $state->setReplacement($stateList);
        $state->getHeaderPrototype()->style['width'] = '15%';

        //actions
        $grid->addActionHref('detail', NULL, 'Catalog:edit')->setIcon('pencil');
        $grid->addActionHref('remove', NULL, $this->getName() . '-delete!')
            ->setIcon('trash')
            ->setCustomRender(function (Catalog $catalog) {
                return sprintf('<a class="grid-action-remove btn btn-default btn-xs btn-mini" 
                                  data-grido-confirm="Opravdu si přejete smazat \'%s\' ?" 
                                  href="%s"><i class="fa fa-trash"></i></a>', $catalog->getTranslation()->getTitle(), $this->link('delete!', ['id' => $catalog->getId()]));
            });

        return $grid;
    }



    /**
     * @param $id int
     * @throws AbortException
     */
    public function handleDelete(int $id)
    {
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $catalogFacade = $this->catalogFacadeFactory->create();
            $catalogFacade->delete($id);
            $this->database->commit();

            $presenter->flashMessage('Položka byla smazána.', 'success');
            $presenter->redirect('this');
        } catch (CatalogFacadeException $exception) {
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