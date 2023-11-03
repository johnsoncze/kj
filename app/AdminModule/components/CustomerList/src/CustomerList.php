<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\CustomerList;

use App\Components\GridoComponent;
use App\Customer\CustomerRepository;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use Grido\Grid;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CustomerList extends GridoComponent
{


    /** @var CustomerRepository */
    private $customerRepo;



    public function __construct(CustomerRepository $customerRepository,
                                GridoFactory $gridoFactory)
    {
        parent::__construct($gridoFactory);
        $this->customerRepo = $customerRepository;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $source = new RepositorySource($this->customerRepo);
        $source->setDefaultSort(['lastName', 'firstName'], 'ASC');

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $grid->addColumnText('lastName', 'Příjmení')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('firstName', 'Křestní jméno')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('email', 'E-mail')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('externalSystemId', 'Id v externím systému')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('code', 'Kód zákazníka')
            ->setSortable()
            ->setFilterText();

        //actions
        $grid->addActionHref('detail', '', 'Customer:detail')
            ->setIcon('eye');

        //styles
        $grid->getColumn('lastName')->getHeaderPrototype()->style['width'] = '15%';
        $grid->getColumn('firstName')->getHeaderPrototype()->style['width'] = '15%';
        $grid->getColumn('email')->getHeaderPrototype()->style['width'] = '25%';
        $grid->getColumn('externalSystemId')->getHeaderPrototype()->style['width'] = '15%';
        $grid->getColumn('code')->getHeaderPrototype()->style['width'] = '10%';

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}