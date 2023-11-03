<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Company\Preview;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PreviewFactory
{


    /**
     * @return Preview
     */
    public function create();
}