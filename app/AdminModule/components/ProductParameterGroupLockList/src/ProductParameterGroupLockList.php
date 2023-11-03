<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductParameterGroupLockList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\ProductParameterGroup\Lock\LockRepository;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use Grido\Grid;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductParameterGroupLockList extends GridoComponent
{


    /** @var ProductParameterGroupEntity|null */
    private $group;

    /** @var LockRepository */
    private $lockRepo;



    public function __construct(GridoFactory $gridoFactory,
                                LockRepository $lockRepo)
    {
        parent::__construct($gridoFactory);
        $this->lockRepo = $lockRepo;
    }



    /**
     * @param $group ProductParameterGroupEntity
     * @return self
     */
    public function setGroup(ProductParameterGroupEntity $group) : self
    {
        $this->group = $group;
        return $this;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        //source
        $source = new RepositorySource($this->lockRepo);
        $source->filter([['groupId', '=', $this->group->getId()]]);

        //grid
        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $grid->addColumnText('key', 'Klíč')->getHeaderPrototype()->style['width'] = '30%';
        $grid->addColumnText('description', 'Popis')->getHeaderPrototype()->style['width'] = '50%';

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}