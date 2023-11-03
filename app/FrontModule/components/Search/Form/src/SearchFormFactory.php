<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Search\Form;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface SearchFormFactory
{


    /**
     * @return SearchForm
     */
    public function create();
}