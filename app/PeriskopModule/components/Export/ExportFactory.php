<?php

declare(strict_types = 1);

namespace App\PeriskopModule\Component\Export;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ExportFactory
{


    /**
     * @return Export
     */
    public function create();
}