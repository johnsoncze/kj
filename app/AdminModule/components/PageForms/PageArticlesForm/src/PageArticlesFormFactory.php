<?php

declare(strict_types = 1);

namespace App\Components\PageArticlesForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PageArticlesFormFactory
{


    /**
     * @return PageArticlesForm
     */
    public function create();
}