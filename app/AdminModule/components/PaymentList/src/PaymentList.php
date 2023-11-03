<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\PaymentList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Payment\Payment;
use App\Payment\PaymentRepositoryFactory;
use App\Payment\Translation\PaymentTranslation;
use Grido\Grid;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PaymentList extends GridoComponent
{


    /** @var PaymentRepositoryFactory */
    protected $paymentRepoFactory;



    public function __construct(GridoFactory $gridoFactory,
                                PaymentRepositoryFactory $paymentRepositoryFactory)
    {
        parent::__construct($gridoFactory);
        $this->paymentRepoFactory = $paymentRepositoryFactory;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $paymentTranslationAnnotation = PaymentTranslation::getAnnotation();
        $table = $paymentTranslationAnnotation->getTable();
        $name = $paymentTranslationAnnotation->getPropertyByName('name');

        $states = Payment::getStates();
        $stateList = Arrays::toPair($states, 'key', 'translation');

        $paymentRepo = $this->paymentRepoFactory->create();
        $source = new RepositorySource($paymentRepo);
        $source->setDefaultSort('sort', 'ASC');

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        $grid->addColumnText('name', 'Název')
            ->setColumn(sprintf(':%s.%s', $table->getName(), $name->getColumn()->getName()))
            ->setCustomRender(function(Payment $payment){
                $translation = $payment->getTranslation();
                return $translation->getName();
            })
            ->setSortable()
            ->setFilterText();
        $grid->getColumn('name')->getHeaderPrototype()->style['width'] = '50%';
        $grid->addColumnNumber('price', 'Cena')
            ->setNumberFormat(2, ',', ' ');
        $grid->addColumnNumber('vat', 'DPH')
            ->setNumberFormat(2, ',', ' ');
        $grid->getColumn('price')->getHeaderPrototype()->style['width'] = '15%';
        $grid->addColumnText('state', 'Stav')
            ->setReplacement($stateList)
            ->setSortable()
            ->setFilterSelect(Arrays::mergeTree(['' => ''], $stateList));

        //styles
        $grid->getColumn('name')->getHeaderPrototype()->style['width'] = '45%';
        $grid->getColumn('price')->getHeaderPrototype()->style['width'] = '15%';
        $grid->getColumn('vat')->getHeaderPrototype()->style['width'] = '15%';
        $grid->getColumn('state')->getHeaderPrototype()->style['width'] = '15%';

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}