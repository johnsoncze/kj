<?php

declare(strict_types = 1);

namespace App\PeriskopModule\Presenters;

use App\PeriskopModule\Component\Export\Export;
use App\PeriskopModule\Component\Export\ExportFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ExportPresenter extends AbstractPresenter
{


    /** @var ExportFactory @inject */
    public $exportFactory;



    /**
     * @return Export
     */
    public function createComponentExportCustomer() : Export
    {
        $export = $this->exportFactory->create();
        $export->setType(\App\Periskop\Export\Export::TYPE_CUSTOMER);
        return $export;
    }



    /**
     * @return Export
     */
    public function createComponentExportOrder() : Export
    {
        $export = $this->exportFactory->create();
        $export->setType(\App\Periskop\Export\Export::TYPE_ORDER);
        return $export;
    }
}