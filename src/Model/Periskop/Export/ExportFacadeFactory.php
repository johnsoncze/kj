<?php

declare(strict_types = 1);

namespace App\Periskop\Export;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ExportFacadeFactory
{


    /**
     * @return ExportFacade
     */
    public function create();
}